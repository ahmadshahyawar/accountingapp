<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\Person;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\StockMove;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function trialBalance()
    {
        $accounts = Account::where('is_group', false)->orderBy('code')->get();

        $rows = $accounts->map(function (Account $account) {
            $debit = (float) $account->journalLines()->sum('base_debit');
            $credit = (float) $account->journalLines()->sum('base_credit');

            return [
                'account' => $account,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $account->normal_balance === 'debit' ? $debit - $credit : $credit - $debit,
            ];
        })->filter(fn ($row) => $row['debit'] != 0 || $row['credit'] != 0)->values();

        return view('reports.trial-balance', [
            'rows' => $rows,
            'totalDebit' => $rows->sum('debit'),
            'totalCredit' => $rows->sum('credit'),
        ]);
    }

    public function accountStatement(Request $request)
    {
        $accounts = Account::where('is_group', false)->orderBy('code')->get();
        $accountId = $request->get('account_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $lines = collect();
        $account = null;
        $openingBalance = 0;

        if ($accountId) {
            $account = Account::findOrFail($accountId);

            // No "from" means the statement starts at the beginning of history, so there is
            // nothing to carry forward — only compute a prior-period balance when one is asked for.
            $openingBalance = $from
                ? $account->journalLines()
                    ->whereHas('journalEntry', fn ($q) => $q->where('date', '<', $from))
                    ->get()
                    ->reduce(function ($carry, $line) use ($account) {
                        $delta = $account->normal_balance === 'debit' ? $line->base_debit - $line->base_credit : $line->base_credit - $line->base_debit;

                        return $carry + $delta;
                    }, 0)
                : 0;

            $lines = $account->journalLines()
                ->with(['journalEntry', 'person'])
                ->whereHas('journalEntry', function ($q) use ($from, $to) {
                    $q->when($from, fn ($q2) => $q2->where('date', '>=', $from))
                        ->when($to, fn ($q2) => $q2->where('date', '<=', $to));
                })
                ->get()
                ->sortBy(fn ($line) => $line->journalEntry->date)
                ->values();
        }

        return view('reports.account-statement', compact('accounts', 'account', 'lines', 'from', 'to', 'openingBalance', 'accountId'));
    }

    public function dayBook(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $entries = JournalEntry::with(['lines.account', 'lines.person'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')->orderBy('id')
            ->get();

        return view('reports.day-book', compact('entries', 'from', 'to'));
    }

    public function debtors()
    {
        $arAccount = Account::where('code', '1200')->first();
        $rows = $this->personBalances($arAccount, 'debit');

        return view('reports.debtors', ['rows' => $rows, 'baseCurrency' => \App\Models\Currency::where('is_base', true)->first()]);
    }

    public function creditors()
    {
        $apAccount = Account::where('code', '2100')->first();
        $rows = $this->personBalances($apAccount, 'credit');

        return view('reports.creditors', ['rows' => $rows, 'baseCurrency' => \App\Models\Currency::where('is_base', true)->first()]);
    }

    /** مفاد و ضرر — a profit & loss statement, one of the old app's core financial reports this app never had at all. */
    public function profitAndLoss(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $revenueAccounts = $this->accountsWithPeriodBalance('revenue', $from, $to);
        $expenseAccounts = $this->accountsWithPeriodBalance('expense', $from, $to);

        $totalRevenue = $revenueAccounts->sum('balance');
        $totalExpense = $expenseAccounts->sum('balance');

        return view('reports.profit-and-loss', [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfit' => $totalRevenue - $totalExpense,
            'from' => $from,
            'to' => $to,
        ]);
    }

    private function accountsWithPeriodBalance(string $type, ?string $from, ?string $to)
    {
        return Account::where('type', $type)->where('is_group', false)->orderBy('code')->get()
            ->map(function (Account $account) use ($from, $to) {
                $lines = $account->journalLines()
                    ->whereHas('journalEntry', function ($q) use ($from, $to) {
                        $q->when($from, fn ($q2) => $q2->where('date', '>=', $from))
                            ->when($to, fn ($q2) => $q2->where('date', '<=', $to));
                    })
                    ->get();

                $debit = (float) $lines->sum('base_debit');
                $credit = (float) $lines->sum('base_credit');
                $balance = $account->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;

                return ['account' => $account, 'balance' => $balance];
            })
            ->filter(fn ($row) => abs($row['balance']) > 0.005)
            ->values();
    }

    /** گزارش صندوق و بانک — every cashbox's and bank account's current balance at a glance, another old-app report this app never had. */
    public function cashAndBank()
    {
        return view('reports.cash-and-bank', [
            'cashboxes' => Cashbox::with(['currency', 'account'])->orderBy('name')->get(),
            'bankAccounts' => BankAccount::with(['currency', 'account'])->orderBy('name')->get(),
        ]);
    }

    public function salesGraph()
    {
        $monthly = SalesInvoice::selectRaw("strftime('%Y-%m', date) as month, SUM(total_amount) as total")
            ->groupBy('month')->orderBy('month')->get();

        return view('reports.sales-graph', compact('monthly'));
    }

    /** کاردکس — an item's full stock movement history with a running on-hand balance, the old app screen this app never had. */
    public function kardex(Request $request)
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $itemId = $request->get('item_id');
        $warehouseId = $request->get('warehouse_id');

        $item = null;
        $rows = collect();
        $totalIn = 0;
        $totalOut = 0;

        if ($itemId) {
            $item = Item::findOrFail($itemId);

            $balance = 0;
            $rows = StockMove::where('item_id', $itemId)
                ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
                ->with('warehouse')
                ->orderBy('date')->orderBy('id')
                ->get()
                ->map(function ($move) use (&$balance, &$totalIn, &$totalOut) {
                    $balance += $move->type === 'in' ? $move->quantity : -$move->quantity;
                    $move->type === 'in' ? $totalIn += $move->quantity : $totalOut += $move->quantity;
                    ['number' => $number, 'person' => $person] = $this->stockMoveReference($move);

                    return [
                        'move' => $move,
                        'balance' => $balance,
                        'number' => $number,
                        'person' => $person,
                    ];
                });
        }

        return view('reports.kardex', compact('items', 'warehouses', 'item', 'rows', 'itemId', 'warehouseId', 'totalIn', 'totalOut'));
    }

    /** Matches the old app's کاردکس, which shows the invoice number and the خریدار/فروشنده (buyer/seller) beside each stock move. */
    private function stockMoveReference(StockMove $move): array
    {
        $reference = match ($move->reference_type) {
            'sales_invoice' => SalesInvoice::with('customer')->find($move->reference_id),
            'purchase_invoice' => PurchaseInvoice::with('supplier')->find($move->reference_id),
            'sales_return' => SalesReturn::with('customer')->find($move->reference_id),
            'purchase_return' => PurchaseReturn::with('supplier')->find($move->reference_id),
            'item_transfer' => ItemTransfer::find($move->reference_id),
            default => null,
        };

        return [
            'number' => $reference?->number,
            'person' => $reference?->customer ?? $reference?->supplier,
        ];
    }

    private function personBalances(?Account $account, string $side)
    {
        if (! $account) {
            return collect();
        }

        return JournalLine::where('account_id', $account->id)
            ->whereNotNull('person_id')
            ->with('person')
            ->get()
            ->groupBy('person_id')
            ->map(function ($lines) use ($side) {
                $debit = $lines->sum('base_debit');
                $credit = $lines->sum('base_credit');

                return [
                    'person' => $lines->first()->person,
                    'balance' => $side === 'debit' ? $debit - $credit : $credit - $debit,
                ];
            })
            ->filter(fn ($row) => abs($row['balance']) > 0.005)
            ->sortByDesc('balance')
            ->values();
    }
}
