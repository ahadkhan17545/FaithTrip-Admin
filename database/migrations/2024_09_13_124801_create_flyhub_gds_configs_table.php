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
        Schema::create('flyhub_gds_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gds_id')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            $table->string('secret_code')->nullable();
            $table->tinyInteger('is_production')->comment('0=>No; 1=>Yes');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flyhub_gds_configs');
    }
};
