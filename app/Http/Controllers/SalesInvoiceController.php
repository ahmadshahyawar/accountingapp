<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\JournalLine;
use App\Models\Person;
use App\Models\SalesInvoice;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        $invoices = SalesInvoice::with(['customer', 'currency'])->orderByDesc('date')->orderByDesc('id')->get();

        return view('sales-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $arAccount = Account::where('code', '1200')->first();

        return view('sales-invoices.form', [
            'customers' => Person::customers()->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'items' => Item::with('unit')->orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
            'nextNumber' => 'INV-'.now()->format('Ymd').'-'.str_pad((string) (SalesInvoice::count() + 1), 4, '0', STR_PAD_LEFT),
            'balances' => $this->arBalances($arAccount),
            'proformas' => \App\Models\ProformaInvoice::latest('date')->limit(30)->get(),
        ]);
    }

    /** Matches the old app's "حساب قبلی" (previous balance) box — every customer's running AR balance, keyed by person_id. */
    private function arBalances(?Account $account): array
    {
        if (! $account) {
            return [];
        }

        return JournalLine::where('account_id', $account->id)
            ->whereNotNull('person_id')
            ->get()
            ->groupBy('person_id')
            ->map(fn ($lines) => (float) $lines->sum('base_debit') - (float) $lines->sum('base_credit'))
            ->all();
    }

    public function store(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'person_id' => 'required|exists:persons,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'expense' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.0001',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount' => 'nullable|numeric|min:0',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $arAccount = Account::where('code', '1200')->firstOrFail();
        $revenueAccount = Account::where('code', '4100')->firstOrFail();
        $cogsAccount = Account::where('code', '5100')->firstOrFail();
        $inventoryAccount = Account::where('code', '1300')->firstOrFail();
        $cashbox = Cashbox::first();

        $invoice = DB::transaction(function () use ($data, $fiscalYear, $arAccount, $revenueAccount, $cogsAccount, $inventoryAccount, $cashbox, $ledger) {
            $number = 'INV-'.now()->format('Ymd').'-'.str_pad((string) (SalesInvoice::count() + 1), 4, '0', STR_PAD_LEFT);

            $lineTotalSum = 0;
            $totalCost = 0;
            $lineData = [];

            foreach ($data['lines'] as $line) {
                $item = Item::findOrFail($line['item_id']);
                $lineTotal = ($line['quantity'] * $line['unit_price']) - ($line['discount'] ?? 0);
                $lineTotalSum += $lineTotal;
                $totalCost += $item->cost_price * $line['quantity'];

                $lineData[] = [
                    'item_id' => $item->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => $line['discount'] ?? 0,
                    'total' => $lineTotal,
                ];
            }

            $discount = $data['discount'] ?? 0;
            $expense = $data['expense'] ?? 0;
            $paidAmount = $data['paid_amount'] ?? 0;
            $totalAmount = $lineTotalSum - $discount + $expense;

            $invoice = SalesInvoice::create([
                'number' => $number,
                'date' => $data['date'],
                'person_id' => $data['person_id'],
                'warehouse_id' => $data['warehouse_id'],
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'expense' => $expense,
                'paid_amount' => $paidAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lineData as $line) {
                $invoice->lines()->create($line);

                StockMove::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'date' => $data['date'],
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'unit_cost' => Item::find($line['item_id'])->cost_price,
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                ]);
            }

            $entry = $ledger->postEntry([
                'fiscal_year_id' => $fiscalYear->id,
                'date' => $data['date'],
                'reference_type' => 'sales_invoice',
                'reference_id' => $invoice->id,
                'description' => "فاکتور فروش {$number}",
                'currency_id' => $data['currency_id'],
                'fx_rate' => $data['fx_rate'],
                'lines' => [
                    ['account_id' => $arAccount->id, 'person_id' => $data['person_id'], 'debit' => $totalAmount, 'credit' => 0],
                    ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $totalAmount],
                    // COGS is always posted in base currency (fx_rate 1) since it's derived from item cost, not the invoice currency.
                ],
            ]);

            // Matches the old form's "دریافت نقدی" button — an amount the customer pays on the spot,
            // posted as its own entry against the default cashbox rather than folded into the sale itself.
            if ($paidAmount > 0 && $cashbox) {
                $ledger->postEntry([
                    'fiscal_year_id' => $fiscalYear->id,
                    'date' => $data['date'],
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                    'description' => "دریافت نقدی بابت فاکتور {$number}",
                    'currency_id' => $data['currency_id'],
                    'fx_rate' => $data['fx_rate'],
                    'lines' => [
                        ['account_id' => $cashbox->account_id, 'debit' => $paidAmount, 'credit' => 0],
                        ['account_id' => $arAccount->id, 'person_id' => $data['person_id'], 'debit' => 0, 'credit' => $paidAmount],
                    ],
                ]);
            }

            if ($totalCost > 0) {
                $ledger->postEntry([
                    'fiscal_year_id' => $fiscalYear->id,
                    'date' => $data['date'],
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                    'description' => "بهای تمام شده فروش {$number}",
                    'currency_id' => Currency::where('is_base', true)->value('id'),
                    'fx_rate' => 1,
                    'lines' => [
                        ['account_id' => $cogsAccount->id, 'debit' => $totalCost, 'credit' => 0],
                        ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $totalCost],
                    ],
                ]);
            }

            $invoice->update(['journal_entry_id' => $entry->id]);

            return $invoice;
        });

        return redirect()->route('sales-invoices.show', $invoice)->with('success', "فاکتور فروش {$invoice->number} ثبت شد.");
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'warehouse', 'currency', 'lines.item']);

        return view('sales-invoices.show', ['invoice' => $salesInvoice]);
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        DB::transaction(function () use ($salesInvoice) {
            \App\Models\JournalEntry::where('reference_type', 'sales_invoice')->where('reference_id', $salesInvoice->id)->each(fn ($e) => $e->delete());
            StockMove::where('reference_type', 'sales_invoice')->where('reference_id', $salesInvoice->id)->delete();
            $salesInvoice->lines()->delete();
            $salesInvoice->delete();
        });

        return redirect()->route('sales-invoices.index')->with('success', 'فاکتور حذف شد.');
    }
}
