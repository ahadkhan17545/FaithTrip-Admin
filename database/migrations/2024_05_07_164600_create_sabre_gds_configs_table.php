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
        Schema::create('sabre_gds_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gds_id')->nullable();
            $table->string('user_id')->comment('V1:user:group:domain')->nullable();
            $table->string('password')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sabre_gds_configs');
    }
};
