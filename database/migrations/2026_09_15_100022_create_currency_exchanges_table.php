<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_exchanges', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            $table->string('holder_type'); // cashbox | bank — where the physical cash sits
            $table->foreignId('cashbox_id')->nullable()->constrained('cashboxes')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('paid_currency_id')->constrained('currencies');
            $table->decimal('paid_amount', 18, 4);
            $table->decimal('paid_rate', 18, 6); // paid currency -> base
            $table->foreignId('received_currency_id')->constrained('currencies');
            $table->decimal('received_amount', 18, 4);
            $table->decimal('received_rate', 18, 6); // received currency -> base
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_exchanges');
    }
};
