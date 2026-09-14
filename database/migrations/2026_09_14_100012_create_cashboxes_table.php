<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashboxes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // صندوق
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('account_id')->constrained(); // linked ledger account
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashboxes');
    }
};
