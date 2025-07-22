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
        Schema::create('b2b_account_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('b2b_user_id')->nullable();
            $table->double('amount')->default(0);
            $table->longText('details')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2b_account_deductions');
    }
};
