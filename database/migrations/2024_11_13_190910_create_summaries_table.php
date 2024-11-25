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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('name');  // Name field (e.g., Item name)
            $table->decimal('stock_in', 10, 2)->default(0);  // Total stock in
            $table->decimal('stock_out', 10, 2)->default(0); // Total stock out
            $table->decimal('adjustments', 10, 2)->default(0); // Total adjustments
            $table->decimal('balance', 10, 2)->default(0); // Final balance (stock_in - stock_out + adjustments)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
