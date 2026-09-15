<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('item_transfer_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained();
            $table->decimal('quantity', 18, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transfer_lines');
        Schema::dropIfExists('item_transfers');
    }
};
