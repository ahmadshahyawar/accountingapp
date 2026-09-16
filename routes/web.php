<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTransferController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CashVoucherController;
use App\Http\Controllers\CurrencyExchangeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemTransferController;
use App\Http\Controllers\MoneyTransferController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OpeningBalanceController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'restrict.destroy'])->group(function () {

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/profile/password', [AuthController::class, 'showChangePassword'])->name('profile.password');
Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');

Route::middleware('admin')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

Route::middleware('admin')->prefix('backup')->name('backup.')->group(function () {
    Route::get('/download', [BackupController::class, 'download'])->name('download');
    Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
});

// Personal notes — every user manages only their own, so the delete route is
// deliberately named ".remove" rather than ".destroy": the app-wide
// RestrictDestroyToAdmin middleware gates every "*.destroy" route to admins,
// which would wrongly stop a regular user from deleting their own note.
Route::prefix('notes')->name('notes.')->group(function () {
    Route::get('/', [NoteController::class, 'index'])->name('index');
    Route::post('/', [NoteController::class, 'store'])->name('store');
    Route::post('/{note}/toggle', [NoteController::class, 'toggle'])->name('toggle');
    Route::delete('/{note}', [NoteController::class, 'destroy'])->name('remove');
});

Route::resource('accounts', AccountController::class)->except(['show']);
Route::get('/persons-phonebook', [PersonController::class, 'phonebook'])->name('persons.phonebook');
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
    Route::get('/kardex', [ReportController::class, 'kardex'])->name('kardex');
    Route::get('/profit-and-loss', [ReportController::class, 'profitAndLoss'])->name('profit-and-loss');
    Route::get('/cash-and-bank', [ReportController::class, 'cashAndBank'])->name('cash-and-bank');
    Route::get('/negative-stock', [ReportController::class, 'negativeStock'])->name('negative-stock');
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
    Route::put('/company', [SettingsController::class, 'updateCompany'])->name('company.update');
});

}); // end auth + restrict.destroy group
