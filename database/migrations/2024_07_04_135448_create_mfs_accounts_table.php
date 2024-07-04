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
        Schema::create('mfs_accounts', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('account_type')->comment("1=>Bkash; 2=>Nagad; 3=>Rocket; 4=>Upay; 5=>Sure Cash")->nullable();
            $table->string('acc_no')->nullable();
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
        Schema::dropIfExists('mfs_accounts');
    }
};
