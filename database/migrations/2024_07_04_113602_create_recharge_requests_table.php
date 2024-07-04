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
        Schema::create('recharge_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_bank_account_id')->nullable();
            $table->unsignedBigInteger('admin_mfs_account_id')->nullable();
            $table->tinyInteger('payment_method')->comment("1=>Bank Transfer; 1=>Bank Cheque; 3=>Bkash; 4=>Nagad; 5=>Rocket; 6=>Upay; 7=>Sure Cash")->nullable();
            $table->string('acc_holder_name')->nullable();
            $table->string('acc_no')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('cheque_bank_name')->nullable();
            $table->string('deposite_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('routing_no')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('mobile_no')->nullable();
            $table->double('recharge_amount')->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('attachment')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->comment("0=>Pending; 1=>Approved; 3=>Denied")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharge_requests');
    }
};
