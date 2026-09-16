<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\CashVoucher;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\JournalLine;
use App\Models\Person;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class CashVoucherController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');

        $vouchers = CashVoucher::with(['person', 'cashbox', 'bankAccount', 'currency'])
            ->when($type !== 'all', fn ($q) => $q->where('type', $type))
            ->orderByDesc('date')->orderByDesc('id')
            ->get();

        return view('cash-vouchers.index', compact('vouchers', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'receipt');

        // Same account store() posts a person-linked voucher against, so the
        // حساب گذشته box always matches what actually happens on save.
        $account = Account::where('code', $type === 'receipt' ? '1200' : '2100')->first();

        return view('cash-vouchers.form', [
            'type' => $type,
            'cashboxes' => Cashbox::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('name')->get(),
            'persons' => Person::orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
            'expenseAccounts' => Account::where('is_group', false)->where('type', 'expense')->orderBy('code')->get(),
            'revenueAccounts' => Account::where('is_group', false)->where('type', 'revenue')->orderBy('code')->get(),
            'nextNumber' => 'CV-'.now()->format('Ymd').'-'.str_pad((string) (CashVoucher::count() + 1), 4, '0', STR_PAD_LEFT),
            'balances' => $this->personBalances($account, $type === 'receipt' ? 'debit' : 'credit'),
        ]);
    }

    /** Matches the old app's "حساب گذشته" (previous balance) box — every person's running balance on the account this voucher type posts against. */
    private function personBalances(?Account $account, string $side): array
    {
        if (! $account) {
            return [];
        }

        return JournalLine::where('account_id', $account->id)
            ->whereNotNull('person_id')
            ->get()
            ->groupBy('person_id')
            ->map(fn ($lines) => $side === 'debit'
                ? (float) $lines->sum('base_debit') - (float) $lines->sum('base_credit')
                : (float) $lines->sum('base_credit') - (float) $lines->sum('base_debit'))
            ->all();
    }

    public function store(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'type' => 'required|in:receipt,payment',
            'date' => 'required|date',
            'source' => 'required|in:cashbox,bank',
            'cashbox_id' => 'required_if:source,cashbox|nullable|exists:cashboxes,id',
            'bank_account_id' => 'required_if:source,bank|nullable|exists:bank_accounts,id',
            'person_id' => 'nullable|exists:persons,id',
            'contra_account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'description' => 'nullable|string|max:255',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $moneyAccount = $data['source'] === 'cashbox'
            ? Cashbox::findOrFail($data['cashbox_id'])->account_id
            : BankAccount::findOrFail($data['bank_account_id'])->account_id;

        // Contra side: prefer the linked person's AR/AP account, else the chosen expense/revenue account.
        $contraAccountId = $data['contra_account_id'] ?? null;
        if (! $contraAccountId && $data['person_id']) {
            $contraAccountId = Account::where('code', $data['type'] === 'receipt' ? '1200' : '2100')->value('id');
        }
        abort_if(! $contraAccountId, 422, 'حساب مقابل (شخص یا حساب مصرف/عواید) مشخص نشده است.');

        $number = 'CV-'.now()->format('Ymd').'-'.str_pad((string) (CashVoucher::count() + 1), 4, '0', STR_PAD_LEFT);

        $entry = $ledger->postEntry([
            'fiscal_year_id' => $fiscalYear->id,
            'date' => $data['date'],
            'reference_type' => 'cash_voucher',
            'reference_id' => null,
            'description' => $data['description'] ?? ($data['type'] === 'receipt' ? 'دریافت نقدی' : 'پرداخت نقدی'),
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'lines' => $data['type'] === 'receipt'
                ? [
                    ['account_id' => $moneyAccount, 'person_id' => null, 'debit' => $data['amount'], 'credit' => 0],
                    ['account_id' => $contraAccountId, 'person_id' => $data['person_id'] ?? null, 'debit' => 0, 'credit' => $data['amount']],
                ]
                : [
                    ['account_id' => $contraAccountId, 'person_id' => $data['person_id'] ?? null, 'debit' => $data['amount'], 'credit' => 0],
                    ['account_id' => $moneyAccount, 'person_id' => null, 'debit' => 0, 'credit' => $data['amount']],
                ],
        ]);

        CashVoucher::create([
            'number' => $number,
            'type' => $data['type'],
            'date' => $data['date'],
            'cashbox_id' => $data['source'] === 'cashbox' ? $data['cashbox_id'] : null,
            'bank_account_id' => $data['source'] === 'bank' ? $data['bank_account_id'] : null,
            'person_id' => $data['person_id'] ?? null,
            'amount' => $data['amount'],
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'description' => $data['description'] ?? null,
            'journal_entry_id' => $entry->id,
        ]);

        return redirect()->route('cash-vouchers.index')
            ->with('success', ($data['type'] === 'receipt' ? 'رسید دریافت' : 'رسید پرداخت')." {$number} ثبت شد.");
    }

    public function destroy(CashVoucher $cashVoucher)
    {
        if ($cashVoucher->journalEntry) {
            $cashVoucher->journalEntry->delete(); // cascades to journal_lines
        }
        $cashVoucher->delete();

        return redirect()->route('cash-vouchers.index')->with('success', 'رسید حذف شد.');
    }
}
