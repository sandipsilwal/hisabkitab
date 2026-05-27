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
        Schema::create('game_types', function (Blueprint $table) {
            $table->id();
            $table->string('game_name');
            $table->timestamps();
        });

        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('default_times', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('minutes');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_type_id')->constrained('game_types')->onDelete('cascade');
            $table->foreignId('default_time_id')->constrained('default_times')->onDelete('cascade');
            $table->integer('rate');
            $table->timestamps();
        });

        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('game_type_id')->constrained('game_types')->onDelete('cascade');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_type_id')->constrained('game_types')->onDelete('cascade');
            $table->integer('time_per_day'); // In minutes
            $table->integer('no_of_days');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
        Schema::dropIfExists('tokens');
        Schema::dropIfExists('rates');
        Schema::dropIfExists('default_times');
        Schema::dropIfExists('payment_types');
        Schema::dropIfExists('game_types');
    }
};
