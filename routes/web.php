<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTransferController;
use App\Http\Controllers\CashVoucherController;
use App\Http\Controllers\CurrencyExchangeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemTransferController;
use App\Http\Controllers\MoneyTransferController;
use App\Http\Controllers\OpeningBalanceController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('accounts', AccountController::class)->except(['show']);
Route::resource('persons', PersonController::class)->except(['show']);
Route::resource('items', ItemController::class)->except(['show']);

Route::resource('cash-vouchers', CashVoucherController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('money-transfers', MoneyTransferController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('account-transfers', AccountTransferController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('currency-exchanges', CurrencyExchangeController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('item-transfers', ItemTransferController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('proforma-invoices', ProformaInvoiceController::class)->only(['index', 'create', 'store', 'destroy']);

Route::resource('sales-invoices', SalesInvoiceController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::resource('purchase-invoices', PurchaseInvoiceController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::resource('sales-returns', SalesReturnController::class)->only(['index', 'create', 'store', 'destroy']);
Route::resource('purchase-returns', PurchaseReturnController::class)->only(['index', 'create', 'store', 'destroy']);

Route::prefix('opening-balances')->name('opening-balances.')->group(function () {
    Route::get('/', [OpeningBalanceController::class, 'index'])->name('index');
    Route::post('/accounts', [OpeningBalanceController::class, 'saveAccounts'])->name('accounts');
    Route::post('/persons', [OpeningBalanceController::class, 'savePersons'])->name('persons');
    Route::post('/items', [OpeningBalanceController::class, 'saveItems'])->name('items');
    Route::post('/post', [OpeningBalanceController::class, 'post'])->name('post');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
    Route::get('/account-statement', [ReportController::class, 'accountStatement'])->name('account-statement');
    Route::get('/day-book', [ReportController::class, 'dayBook'])->name('day-book');
    Route::get('/debtors', [ReportController::class, 'debtors'])->name('debtors');
    Route::get('/creditors', [ReportController::class, 'creditors'])->name('creditors');
    Route::get('/sales-graph', [ReportController::class, 'salesGraph'])->name('sales-graph');
});

Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/units', [SettingsController::class, 'storeUnit'])->name('units.store');
    Route::delete('/units/{unit}', [SettingsController::class, 'destroyUnit'])->name('units.destroy');
    Route::post('/warehouses', [SettingsController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::delete('/warehouses/{warehouse}', [SettingsController::class, 'destroyWarehouse'])->name('warehouses.destroy');
    Route::post('/currencies', [SettingsController::class, 'storeCurrency'])->name('currencies.store');
    Route::post('/exchange-rates', [SettingsController::class, 'storeExchangeRate'])->name('exchange-rates.store');
    Route::post('/fiscal-years', [SettingsController::class, 'storeFiscalYear'])->name('fiscal-years.store');
    Route::post('/fiscal-years/{fiscalYear}/activate', [SettingsController::class, 'activateFiscalYear'])->name('fiscal-years.activate');
    Route::post('/cashboxes', [SettingsController::class, 'storeCashbox'])->name('cashboxes.store');
    Route::post('/bank-accounts', [SettingsController::class, 'storeBankAccount'])->name('bank-accounts.store');
});
