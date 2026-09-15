<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            $table->foreignId('from_account_id')->constrained('accounts');
            $table->foreignId('from_person_id')->nullable()->constrained('persons')->nullOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts');
            $table->foreignId('to_person_id')->nullable()->constrained('persons')->nullOnDelete();
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
        Schema::dropIfExists('account_transfers');
    }
};
