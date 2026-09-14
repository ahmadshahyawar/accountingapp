<?php

namespace App\Providers;

use App\Models\CashVoucher;
use App\Models\OpeningBalance;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Short, stable names stored in journal_entries.reference_type / stock_moves.reference_type
        // instead of full class names, so renaming a model doesn't orphan historical postings.
        Relation::enforceMorphMap([
            'sales_invoice' => SalesInvoice::class,
            'purchase_invoice' => PurchaseInvoice::class,
            'cash_voucher' => CashVoucher::class,
            'opening_balance' => OpeningBalance::class,
        ]);
    }
}
