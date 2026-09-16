<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // The old app's تعریف شخص form has separate موبایل and تلفن
            // fields — "phone" here was already standing in for تلفن only.
            $table->string('mobile')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('mobile');
        });
    }
};
