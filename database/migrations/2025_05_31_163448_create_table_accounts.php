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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_default_cash_account')->default(0);
            $table->boolean('is_default_online_account')->default(0);
            $table->integer('balance');
            $table->timestamps();
        });
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date_bs');
            $table->date('date_ad');
            $table->integer('amount');
            $table->unsignedBigInteger('to_account_id');
            $table->string('remarks')->nullable();
            $table->boolean('is_posted');
            $table->timestamps();

            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date_bs');
            $table->date('date_ad');
            $table->integer('amount');
            $table->unsignedBigInteger('from_account_id');
            $table->unsignedBigInteger('to_account_id');
            $table->string('remarks')->nullable();
            $table->boolean('is_posted');
            $table->timestamps();

            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('date_bs');
            $table->date('date_ad');
            $table->string('title');
            $table->integer('amount');
            $table->unsignedBigInteger('from_account_id');
            $table->string('remarks')->nullable();
            $table->boolean('is_posted');
            $table->timestamps();

            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        Schema::create('extra_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('date_bs');
            $table->date('date_ad');
            $table->string('title');
            $table->integer('amount');
            $table->unsignedBigInteger('to_account_id');
            $table->string('remarks')->nullable();
            $table->boolean('is_posted');
            $table->timestamps();

            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_incomes');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('account_transaction');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
    }
};
