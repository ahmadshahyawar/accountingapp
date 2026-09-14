<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
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
        ]);
    }
}
