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
        Schema::create('ssl_commerz_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recharge_history_id')->nullable();
            $table->string('tran_id')->nullable();
            $table->string('bank_tran_id')->nullable();
            $table->string('val_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('card_type')->nullable();
            $table->string('store_amount')->nullable();
            $table->string('card_no')->nullable();
            $table->string('status')->nullable();
            $table->string('tran_date')->nullable();
            $table->string('currency')->nullable();
            $table->string('card_issuer')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_sub_brand')->nullable();
            $table->string('card_issuer_country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssl_commerz_payment_histories');
    }
};
