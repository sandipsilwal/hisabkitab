<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skaters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('membership_no')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('shoes_size')->nullable();
            $table->timestamps();
        });
        Schema::create('skater_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skater_id');
            $table->string('no_of_skaters');
            $table->integer('session_minutes');
            $table->integer('amount');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('payment_method', ['online', 'cash','unpaid'])->default('cash');
            $table->enum('status', ['active','playing','over_time','completed'])->default('active');
            $table->timestamps();
            $table->foreign('skater_id')->references('id')->on('skaters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skater_histories');
        Schema::dropIfExists('skaters');
    }
};