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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->decimal('cost', 10, 2);
            $table->decimal('price', 10, 2);
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->integer('quantity')->default(0);
            // $table->integer('initial_quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
