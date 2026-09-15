<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\CurrencyExchange;
use Illuminate\Http\Request;

class CurrencyExchangeController extends Controller
{
    public function index()
    {
        $exchanges = CurrencyExchange::with(['cashbox', 'bankAccount', 'paidCurrency', 'receivedCurrency'])
            ->orderByDesc('date')->orderByDesc('id')->get();

        return view('currency-exchanges.index', compact('exchanges'));
    }

    public function create()
    {
        return view('currency-exchanges.form', [
            'cashboxes' => Cashbox::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('name')->get(),
            'currencies' => \App\Models\Currency::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'holder_type' => 'required|in:cashbox,bank',
            'cashbox_id' => 'required_if:holder_type,cashbox|nullable|exists:cashboxes,id',
            'bank_account_id' => 'required_if:holder_type,bank|nullable|exists:bank_accounts,id',
            'paid_currency_id' => 'required|exists:currencies,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_rate' => 'required|numeric|min:0.000001',
            'received_currency_id' => 'required|exists:currencies,id|different:paid_currency_id',
            'received_amount' => 'required|numeric|min:0.01',
            'received_rate' => 'required|numeric|min:0.000001',
            'description' => 'nullable|string|max:255',
        ]);

        $number = 'FX-'.now()->format('Ymd').'-'.str_pad((string) (CurrencyExchange::count() + 1), 4, '0', STR_PAD_LEFT);

        CurrencyExchange::create([
            'number' => $number,
            'date' => $data['date'],
            'holder_type' => $data['holder_type'],
            'cashbox_id' => $data['holder_type'] === 'cashbox' ? $data['cashbox_id'] : null,
            'bank_account_id' => $data['holder_type'] === 'bank' ? $data['bank_account_id'] : null,
            'paid_currency_id' => $data['paid_currency_id'],
            'paid_amount' => $data['paid_amount'],
            'paid_rate' => $data['paid_rate'],
            'received_currency_id' => $data['received_currency_id'],
            'received_amount' => $data['received_amount'],
            'received_rate' => $data['received_rate'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('currency-exchanges.index')->with('success', "تبادله ارز {$number} ثبت شد.");
    }

    public function destroy(CurrencyExchange $currencyExchange)
    {
        $currencyExchange->delete();

        return redirect()->route('currency-exchanges.index')->with('success', 'ثبت حذف شد.');
    }
}
