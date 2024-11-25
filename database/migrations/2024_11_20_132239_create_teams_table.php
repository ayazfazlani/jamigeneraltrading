<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Update users table to include team_id
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('id');
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::dropIfExists('teams');
    }
};
