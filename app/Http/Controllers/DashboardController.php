<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\FiscalYear;
use App\Models\Person;
use Morilog\Jalali\Jalalian;

class DashboardController extends Controller
{
    public function index()
    {
        $fiscalYear = FiscalYear::current();

        $debtors = Account::where('code', '1200')->first(); // Accounts Receivable
        $creditors = Account::where('code', '2100')->first(); // Accounts Payable

        return view('dashboard', [
            'fiscalYear' => $fiscalYear,
            'today' => Jalalian::now(),
            'debtorsBalance' => $debtors?->balance() ?? 0,
            'creditorsBalance' => $creditors?->balance() ?? 0,
            'customerCount' => Person::customers()->count(),
            'supplierCount' => Person::suppliers()->count(),
            'baseCurrency' => Currency::where('is_base', true)->first(),
            // The dashboard's exchange-rate tile mirrors the original app's rotating
            // ticker — showing the most recently set rate for a non-base currency.
            'topExchangeRate' => ExchangeRate::with('currency')
                ->whereHas('currency', fn ($q) => $q->where('is_base', false))
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->first(),
        ]);
    }
}
