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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable(); // Ensure it is unsigned and nullable for "set null"
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->enum('type', ['stock in', 'stock out', 'adjusted', 'created', 'edit', 'deleted']);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();  // Optional if no unit price is specified
            $table->decimal('total_price', 10, 2);
            $table->date('date')->default(now());  // Consistent with current date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
