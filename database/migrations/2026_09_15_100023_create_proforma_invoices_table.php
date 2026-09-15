<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            // A quote for a walk-in prospect — no account/person required, unlike a real sale.
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('currency_id')->constrained();
            $table->decimal('fx_rate', 18, 6)->default(1);
            $table->decimal('discount', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('proforma_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('total', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_lines');
        Schema::dropIfExists('proforma_invoices');
    }
};
