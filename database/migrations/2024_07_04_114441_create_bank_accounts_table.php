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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('routing_no')->nullable();
            $table->string('acc_holder_name')->nullable();
            $table->string('acc_no')->nullable();
            $table->string('swift_code')->nullable();
            $table->tinyInteger('status')->comment("0=>Inactive; 1=>Active")->nullable();
            $table->string('slug')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
