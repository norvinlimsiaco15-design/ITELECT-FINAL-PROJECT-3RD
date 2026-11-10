<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('price');
            $table->decimal('quantity', 15, 3)->default(0)->after('unit');
            $table->decimal('min_stock_level', 15, 3)->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['unit', 'quantity', 'min_stock_level']);
        });
    }
};
