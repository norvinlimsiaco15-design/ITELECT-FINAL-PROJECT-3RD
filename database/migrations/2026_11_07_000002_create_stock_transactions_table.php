<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->decimal('quantity', 15, 3);
            $table->decimal('balance_after', 15, 3)->default(0);
            $table->date('transaction_date');
            $table->string('party_name')->nullable();
            $table->text('party_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
