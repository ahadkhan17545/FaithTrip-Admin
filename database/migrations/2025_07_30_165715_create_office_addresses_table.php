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
        Schema::create('office_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->nullable();
            $table->longText('office_address')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->comment("0=>Inactive; 1=>Active")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_addresses');
    }
};
