<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransfer;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Person;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class AccountTransferController extends Controller
{
    public function index()
    {
        $transfers = AccountTransfer::with(['fromAccount', 'fromPerson', 'toAccount', 'toPerson', 'currency'])
            ->orderByDesc('date')->orderByDesc('id')->get();

        return view('account-transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('account-transfers.form', [
            'accounts' => Account::where('is_group', false)->orderBy('code')->get(),
            'persons' => Person::orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request, LedgerService $ledger)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'from_account_id' => 'required|exists:accounts,id',
            'from_person_id' => 'nullable|exists:persons,id',
            'to_account_id' => 'required|exists:accounts,id',
            'to_person_id' => 'nullable|exists:persons,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'fx_rate' => 'required|numeric|min:0.000001',
            'description' => 'nullable|string|max:255',
        ]);

        $fiscalYear = FiscalYear::current();
        abort_unless($fiscalYear, 422, 'سال مالی جاری تعریف نشده است.');

        $number = 'AT-'.now()->format('Ymd').'-'.str_pad((string) (AccountTransfer::count() + 1), 4, '0', STR_PAD_LEFT);

        $entry = $ledger->postEntry([
            'fiscal_year_id' => $fiscalYear->id,
            'date' => $data['date'],
            'reference_type' => 'account_transfer',
            'reference_id' => null,
            'description' => $data['description'] ?? "انتقال حساب {$number}",
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'lines' => [
                ['account_id' => $data['from_account_id'], 'person_id' => $data['from_person_id'] ?? null, 'debit' => $data['amount'], 'credit' => 0],
                ['account_id' => $data['to_account_id'], 'person_id' => $data['to_person_id'] ?? null, 'debit' => 0, 'credit' => $data['amount']],
            ],
        ]);

        AccountTransfer::create([
            'number' => $number,
            'date' => $data['date'],
            'from_account_id' => $data['from_account_id'],
            'from_person_id' => $data['from_person_id'] ?? null,
            'to_account_id' => $data['to_account_id'],
            'to_person_id' => $data['to_person_id'] ?? null,
            'amount' => $data['amount'],
            'currency_id' => $data['currency_id'],
            'fx_rate' => $data['fx_rate'],
            'description' => $data['description'] ?? null,
            'journal_entry_id' => $entry->id,
        ]);

        return redirect()->route('account-transfers.index')->with('success', "انتقال حساب {$number} ثبت شد.");
    }

    public function destroy(AccountTransfer $accountTransfer)
    {
        if ($accountTransfer->journalEntry) {
            $accountTransfer->journalEntry->delete();
        }
        $accountTransfer->delete();

        return redirect()->route('account-transfers.index')->with('success', 'انتقال حذف شد.');
    }
}
