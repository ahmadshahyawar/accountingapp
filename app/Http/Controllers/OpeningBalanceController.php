<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\OpeningBalance;
use App\Models\Person;
use App\Models\StockMove;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\LedgerService;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        $fiscalYear = FiscalYear::current();

        abort_unless($fiscalYear, 404, 'ابتدا یک سال مالی جاری تعریف کنید (در تنظیمات).');

        $baseCurrency = \App\Models\Currency::where('is_base', true)->firstOrFail();

        $accountBalances = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)
            ->whereNotNull('account_id')->whereNull('person_id')->get()->keyBy('account_id');

        $personBalances = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)
            ->whereNotNull('person_id')->get()->keyBy('person_id');

        $itemBalances = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)
            ->whereNotNull('item_id')->get();

        return view('opening-balances.index', [
            'fiscalYear' => $fiscalYear,
            'baseCurrency' => $baseCurrency,
            'accounts' => Account::where('is_group', false)
                ->whereNotIn('code', ['1200', '2100'])
                ->orderBy('code')->get(),
            'accountBalances' => $accountBalances,
            'customers' => Person::customers()->orderBy('name')->get(),
            'suppliers' => Person::suppliers()->orderBy('name')->get(),
            'personBalances' => $personBalances,
            'items' => Item::with('unit')->orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'itemBalances' => $itemBalances,
            'alreadyPosted' => JournalEntry::where('fiscal_year_id', $fiscalYear->id)
                ->where('reference_type', 'opening_balance')->exists(),
            'arAccount' => Account::where('code', '1200')->first(),
            'apAccount' => Account::where('code', '2100')->first(),
            'inventoryAccount' => Account::where('code', '1300')->first(),
        ]);
    }

    public function saveAccounts(Request $request)
    {
        $fiscalYear = FiscalYear::current();
        $data = $request->validate([
            'lines' => 'array',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);
        $baseCurrencyId = \App\Models\Currency::where('is_base', true)->value('id');

        foreach ($data['lines'] ?? [] as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            OpeningBalance::updateOrCreate(
                ['fiscal_year_id' => $fiscalYear->id, 'account_id' => $line['account_id'], 'person_id' => null, 'item_id' => null],
                ['debit' => $debit, 'credit' => $credit, 'currency_id' => $baseCurrencyId]
            );
        }

        return back()->with('success', 'مانده های ابتدایی حساب ها ذخیره شد.');
    }

    public function savePersons(Request $request)
    {
        $fiscalYear = FiscalYear::current();
        $arAccount = Account::where('code', '1200')->firstOrFail();
        $apAccount = Account::where('code', '2100')->firstOrFail();
        $baseCurrencyId = \App\Models\Currency::where('is_base', true)->value('id');

        $data = $request->validate([
            'lines' => 'array',
            'lines.*.person_id' => 'required|exists:persons,id',
            'lines.*.role' => 'required|in:customer,supplier',
            'lines.*.amount' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['lines'] ?? [] as $line) {
            $amount = (float) ($line['amount'] ?? 0);
            $account = $line['role'] === 'customer' ? $arAccount : $apAccount;

            OpeningBalance::updateOrCreate(
                ['fiscal_year_id' => $fiscalYear->id, 'person_id' => $line['person_id']],
                [
                    'account_id' => $account->id,
                    'debit' => $line['role'] === 'customer' ? $amount : 0,
                    'credit' => $line['role'] === 'supplier' ? $amount : 0,
                    'currency_id' => $baseCurrencyId,
                ]
            );
        }

        return back()->with('success', 'مانده های ابتدایی اشخاص ذخیره شد.');
    }

    public function saveItems(Request $request)
    {
        $fiscalYear = FiscalYear::current();
        $baseCurrencyId = \App\Models\Currency::where('is_base', true)->value('id');

        $data = $request->validate([
            'lines' => 'array',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.warehouse_id' => 'required|exists:warehouses,id',
            'lines.*.quantity' => 'nullable|numeric',
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['lines'] ?? [] as $line) {
            $qty = (float) ($line['quantity'] ?? 0);

            OpeningBalance::updateOrCreate(
                ['fiscal_year_id' => $fiscalYear->id, 'item_id' => $line['item_id'], 'warehouse_id' => $line['warehouse_id']],
                ['quantity' => $qty, 'debit' => $qty * (float) ($line['unit_cost'] ?? 0), 'credit' => 0, 'currency_id' => $baseCurrencyId]
            );
        }

        return back()->with('success', 'موجودی ابتدایی اجناس ذخیره شد.');
    }

    /**
     * Posts everything currently saved in opening_balances into one ledger entry
     * (re-postable: re-running this replaces the previous opening-balance entry
     * for the fiscal year so editing values above and re-posting just works).
     */
    public function post(LedgerService $ledger)
    {
        $fiscalYear = FiscalYear::current();
        $equityAccount = Account::where('code', '3000')->firstOrFail();
        $baseCurrencyId = \App\Models\Currency::where('is_base', true)->value('id');

        DB::transaction(function () use ($fiscalYear, $equityAccount, $baseCurrencyId, $ledger) {
            // Remove any previous opening-balance posting for this fiscal year so this stays idempotent.
            JournalEntry::where('fiscal_year_id', $fiscalYear->id)
                ->where('reference_type', 'opening_balance')
                ->each(fn (JournalEntry $entry) => $entry->delete());

            StockMove::where('reference_type', 'opening_balance')->delete();

            $accountLines = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)
                ->whereNotNull('account_id')->get();

            $lines = [];
            foreach ($accountLines as $ob) {
                if ($ob->debit == 0 && $ob->credit == 0) {
                    continue;
                }
                $lines[] = [
                    'account_id' => $ob->account_id,
                    'person_id' => $ob->person_id,
                    'debit' => (float) $ob->debit,
                    'credit' => (float) $ob->credit,
                    'description' => 'مانده ابتدایی دوره',
                ];
            }

            if (empty($lines)) {
                return;
            }

            $totalDebit = round(array_sum(array_column($lines, 'debit')), 4);
            $totalCredit = round(array_sum(array_column($lines, 'credit')), 4);
            $diff = round($totalDebit - $totalCredit, 4);

            if (abs($diff) > 0.0005) {
                // Plug the imbalance to equity so the entry can post — normal practice when
                // opening balances are re-keyed from an outside source. See plan's Migration approach.
                $lines[] = $diff > 0
                    ? ['account_id' => $equityAccount->id, 'debit' => 0, 'credit' => $diff, 'description' => 'تعدیل مانده ابتدایی (سرمایه)']
                    : ['account_id' => $equityAccount->id, 'debit' => -$diff, 'credit' => 0, 'description' => 'تعدیل مانده ابتدایی (سرمایه)'];
            }

            $ledger->postEntry([
                'fiscal_year_id' => $fiscalYear->id,
                'date' => $fiscalYear->start_date->toDateString(),
                'reference_type' => 'opening_balance',
                'reference_id' => null,
                'description' => 'ثبت مانده های ابتدایی دوره',
                'currency_id' => $baseCurrencyId,
                'lines' => $lines,
            ]);

            // Opening stock quantities — recorded as stock moves so item quantityOnHand() is correct.
            $itemBalances = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)->whereNotNull('item_id')->get();
            foreach ($itemBalances as $ob) {
                if (! $ob->quantity) {
                    continue;
                }
                StockMove::create([
                    'item_id' => $ob->item_id,
                    'warehouse_id' => $ob->warehouse_id,
                    'date' => $fiscalYear->start_date,
                    'type' => 'in',
                    'quantity' => $ob->quantity,
                    'unit_cost' => $ob->quantity != 0 ? $ob->debit / $ob->quantity : 0,
                    'reference_type' => 'opening_balance',
                    'reference_id' => null,
                ]);
            }
        });

        return redirect()->route('opening-balances.index')->with('success', 'مانده های ابتدایی به دفتر کل ثبت شد.');
    }
}
