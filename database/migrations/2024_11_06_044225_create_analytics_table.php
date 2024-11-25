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
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->integer('current_quantity')->default(0);
            $table->decimal('inventory_assets', 15, 2)->default(0);
            $table->decimal('average_quantity', 15, 2)->default(0);
            $table->decimal('turnover_ratio', 15, 2)->default(0);
            $table->integer('stock_out_days_estimate')->default(0);
            $table->integer('total_stock_out')->default(0);
            $table->integer('total_stock_in')->default(0);
            $table->decimal('avg_daily_stock_in', 15, 2)->default(0);
            $table->decimal('avg_daily_stock_out', 15, 2)->default(0);
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
