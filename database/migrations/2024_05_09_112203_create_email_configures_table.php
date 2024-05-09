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
        Schema::create('email_configures', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->integer('port');
            $table->string('email');
            $table->string('password');
            $table->string('mail_from_name')->nullable();
            $table->string('mail_from_email')->nullable();
            $table->tinyInteger('encryption')->default(0)->comment("0=>None; 1=>TLS; 2=>SSL");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configures');
    }
};
