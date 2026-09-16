<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Matches the old app's فاکتور فروش/خرید forms, which show تخفیف
        // (discount), مصارف (expense) and دریافت/پرداخت (immediate cash
        // received/paid against the invoice) boxes that this app never had.
        foreach (['sales_invoices', 'purchase_invoices'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->decimal('discount', 18, 4)->default(0)->after('total_amount');
                $blueprint->decimal('expense', 18, 4)->default(0)->after('discount');
                $blueprint->decimal('paid_amount', 18, 4)->default(0)->after('expense');
            });
        }
    }

    public function down(): void
    {
        foreach (['sales_invoices', 'purchase_invoices'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['discount', 'expense', 'paid_amount']);
            });
        }
    }
};
