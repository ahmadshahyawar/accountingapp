<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            // source/destination money holder — cashbox|bank on each side
            $table->string('from_type');
            $table->foreignId('from_cashbox_id')->nullable()->constrained('cashboxes')->nullOnDelete();
            $table->foreignId('from_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('to_type');
            $table->foreignId('to_cashbox_id')->nullable()->constrained('cashboxes')->nullOnDelete();
            $table->foreignId('to_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('amount', 18, 4);
            $table->foreignId('currency_id')->constrained();
            $table->decimal('fx_rate', 18, 6)->default(1);
            $table->text('description')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_transfers');
    }
};
