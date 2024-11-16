<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('current_quantity')->default(0);
            $table->decimal('inventory_assets', 12, 2)->default(0);
            $table->integer('average_quantity')->nullable();
            $table->decimal('turnover_ratio', 8, 2)->nullable();
            $table->integer('stock_out_days_estimate')->nullable();
            $table->integer('total_stock_out')->default(0);
            $table->integer('total_stock_in')->default(0);
            $table->decimal('avg_daily_stock_in', 10, 2)->nullable();
            $table->decimal('avg_daily_stock_out', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
