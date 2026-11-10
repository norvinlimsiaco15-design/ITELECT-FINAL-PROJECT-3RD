<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('report_date');
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('material_name');
            $table->string('category_name')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('min_stock_level', 15, 3)->default(0);
            $table->decimal('line_value', 15, 2)->default(0);
            $table->boolean('is_low_stock')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_report_items');
        Schema::dropIfExists('inventory_reports');
    }
};
