<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            $table->foreignId('person_id')->constrained('persons'); // customer
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('currency_id')->constrained();
            $table->decimal('fx_rate', 18, 6)->default(1);
            $table->foreignId('sales_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_return_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('total', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_lines');
        Schema::dropIfExists('sales_returns');
    }
};
