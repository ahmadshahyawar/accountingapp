<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\FiscalYear;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class SettingsController extends Controller
{
    // Each ribbon button (سال مالی، نرخ ارز، ارز ها، بانک ها، صندوق، واحد ها،
    // انبار ها، مشخصات شرکت) is its own dedicated screen showing only its own
    // data — matching the old app's one-dialog-per-function structure, not a
    // single page with every unrelated table visible at once.

    public function fiscalYears()
    {
        return view('settings.fiscal-years', [
            'fiscalYears' => FiscalYear::orderByDesc('start_date')->get(),
        ]);
    }

    public function exchangeRates()
    {
        return view('settings.exchange-rates', [
            'currencies' => Currency::with(['exchangeRates' => fn ($q) => $q->latest('effective_date')->limit(5)])->orderBy('code')->get(),
        ]);
    }

    public function currencies()
    {
        return view('settings.currencies', [
            'currencies' => Currency::with(['exchangeRates' => fn ($q) => $q->latest('effective_date')->limit(1)])->orderBy('code')->get(),
        ]);
    }

    public function units()
    {
        return view('settings.units', [
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function warehouses()
    {
        return view('settings.warehouses', [
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function cashboxes()
    {
        return view('settings.cashboxes', [
            'cashboxes' => Cashbox::with(['currency', 'account'])->orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
            'moneyAccounts' => Account::where('is_group', false)->orderBy('code')->get(),
        ]);
    }

    public function bankAccounts()
    {
        return view('settings.bank-accounts', [
            'bankAccounts' => BankAccount::with(['currency', 'account'])->orderBy('name')->get(),
            'currencies' => Currency::orderBy('code')->get(),
            'moneyAccounts' => Account::where('is_group', false)->orderBy('code')->get(),
        ]);
    }

    public function company()
    {
        return view('settings.company', [
            'company' => CompanySetting::current(),
        ]);
    }

    public function backup()
    {
        return view('settings.backup');
    }

    // --- Company info (مشخصات شرکت) ---
    public function updateCompany(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        CompanySetting::current()->update($data);

        return back()->with('success', 'مشخصات شرکت ذخیره شد.');
    }

    // --- Units ---
    public function storeUnit(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:units,name', 'symbol' => 'nullable|string|max:20']);
        Unit::create($data);

        return back()->with('success', 'واحد اضافه شد.');
    }

    public function destroyUnit(Unit $unit)
    {
        $unit->delete();

        return back()->with('success', 'حذف شد.');
    }

    // --- Warehouses ---
    public function storeWarehouse(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:warehouses,name', 'address' => 'nullable|string|max:255']);
        Warehouse::create($data);

        return back()->with('success', 'گدام اضافه شد.');
    }

    public function destroyWarehouse(Warehouse $warehouse)
    {
        $warehouse->delete();

        return back()->with('success', 'حذف شد.');
    }

    // --- Currencies & exchange rates ---
    public function storeCurrency(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
        ]);
        Currency::create($data);

        return back()->with('success', 'واحد پول اضافه شد.');
    }

    public function storeExchangeRate(Request $request)
    {
        $data = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'rate' => 'required|numeric|min:0.000001',
            'effective_date' => 'required|date',
        ]);
        ExchangeRate::updateOrCreate(
            ['currency_id' => $data['currency_id'], 'effective_date' => $data['effective_date']],
            ['rate' => $data['rate']]
        );

        return back()->with('success', 'نرخ ارز ثبت شد.');
    }

    // --- Fiscal years ---
    public function storeFiscalYear(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:fiscal_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        FiscalYear::create($data);

        return back()->with('success', 'سال مالی اضافه شد.');
    }

    public function activateFiscalYear(FiscalYear $fiscalYear)
    {
        FiscalYear::query()->update(['is_current' => false]);
        $fiscalYear->update(['is_current' => true]);

        return back()->with('success', "سال مالی {$fiscalYear->name} به عنوان سال جاری تنظیم شد.");
    }

    // --- Cashboxes & bank accounts ---
    public function storeCashbox(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'account_id' => 'required|exists:accounts,id',
        ]);
        Cashbox::create($data);

        return back()->with('success', 'صندوق اضافه شد.');
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'currency_id' => 'required|exists:currencies,id',
            'account_id' => 'required|exists:accounts,id',
        ]);
        BankAccount::create($data);

        return back()->with('success', 'حساب بانکی اضافه شد.');
    }
}
