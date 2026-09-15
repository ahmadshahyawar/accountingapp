<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\MoneyTransfer;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class MoneyTransferController extends Controller
{
    public function index()
    {
        $transfers = MoneyTransfer::with(['fromCashbox', 'fromBankAccount', 'toCashbox', 'toBankAccount', 'currency'])
            ->orderByDesc('date')->orderByDesc('id')->get();

        return view('money-transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('money-transfers.form', [
            'cashboxes' => Cashbox::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'from_type' => 'required|in:cashbox,bank',
            'from_cashbox_id' => 'required_if:from_type,cashbox|nullable|exists:cashboxes,id',
            'from_bank_account_id' => 'required_if:from_type,bank|nullable|exists:bank_accounts,id',
            'to_type' => 'required|in:cashbox,bank',
            'to_cashbox_id' => 'required_if:to_type,cashbox|nullable|exists:cashboxes,id',
            'to_bank_account_id' => 'required_if:to_type,bank|nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'description' => 'nullable|string|max:255',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $fromAccountId = $data['from_type'] === 'cashbox'
            ? Cashbox::findOrFail($data['from_cashbox_id'])->account_id
            : BankAccount::findOrFail($data['from_bank_account_id'])->account_id;
        $toAccountId = $data['to_type'] === 'cashbox'
            ? Cashbox::findOrFail($data['to_cashbox_id'])->account_id
            : BankAccount::findOrFail($data['to_bank_account_id'])->account_id;

        abort_if($fromAccountId === $toAccountId, 422, 'مبدا و مقصد انتقال نمی‌تواند یکسان باشد.');

        $number = 'MT-'.now()->format('Ymd').'-'.str_pad((string) (MoneyTransfer::count() + 1), 4, '0', STR_PAD_LEFT);

        $entry = $ledger->postEntry([
            'fiscal_year_id' => $fiscalYear->id,
            'date' => $data['date'],
            'reference_type' => 'money_transfer',
            'reference_id' => null,
            'description' => $data['description'] ?? "انتقال پول {$number}",
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'lines' => [
                ['account_id' => $toAccountId, 'debit' => $data['amount'], 'credit' => 0],
                ['account_id' => $fromAccountId, 'debit' => 0, 'credit' => $data['amount']],
            ],
        ]);

        MoneyTransfer::create([
            'number' => $number,
            'date' => $data['date'],
            'from_type' => $data['from_type'],
            'from_cashbox_id' => $data['from_type'] === 'cashbox' ? $data['from_cashbox_id'] : null,
            'from_bank_account_id' => $data['from_type'] === 'bank' ? $data['from_bank_account_id'] : null,
            'to_type' => $data['to_type'],
            'to_cashbox_id' => $data['to_type'] === 'cashbox' ? $data['to_cashbox_id'] : null,
            'to_bank_account_id' => $data['to_type'] === 'bank' ? $data['to_bank_account_id'] : null,
            'amount' => $data['amount'],
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'description' => $data['description'] ?? null,
            'journal_entry_id' => $entry->id,
        ]);

        return redirect()->route('money-transfers.index')->with('success', "انتقال پول {$number} ثبت شد.");
    }

    public function destroy(MoneyTransfer $moneyTransfer)
    {
        if ($moneyTransfer->journalEntry) {
            $moneyTransfer->journalEntry->delete();
        }
        $moneyTransfer->delete();

        return redirect()->route('money-transfers.index')->with('success', 'انتقال حذف شد.');
    }
}
