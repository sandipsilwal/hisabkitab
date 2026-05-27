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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('player_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->integer('total_amount');
            $table->string('package_status')->default('not played'); // not played, started, completed
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('player_package_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_package_id')->constrained('player_packages')->onDelete('cascade');
            $table->date('date');
            $table->integer('amount');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('play_records', function (Blueprint $table) {
            $table->id();
            $table->string('player_type')->default('normal'); // normal, package
            $table->foreignId('player_package_id')->nullable()->constrained('player_packages')->onDelete('cascade');
            $table->foreignId('token_id')->constrained('tokens')->onDelete('cascade');
            $table->integer('default_time')->nullable(); // stored minutes (null represents custom/no limit)
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('actual_time')->nullable(); // stored actual minutes played
            $table->integer('no_of_players')->default(1);
            $table->integer('amount')->default(0);
            $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_records');
        Schema::dropIfExists('player_package_payment_histories');
        Schema::dropIfExists('player_packages');
        Schema::dropIfExists('players');
    }
};
