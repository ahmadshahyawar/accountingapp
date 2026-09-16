<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // The old app's تعریف جنس form has a عکس (photo) uploader next
            // to the barcode — this app had no equivalent at all.
            $table->string('photo_path')->nullable()->after('reorder_level');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
