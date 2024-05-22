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
        Schema::create('flight_passangers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flight_booking_id')->nullable()->comment('FlightBooking Table Id');
            $table->string('passanger_type')->nullable();
            $table->string('title')->nullable()->comment('Mr/Mrs/Miss');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('dob')->nullable();
            $table->string('document_type')->nullable()->comment('1=>Passport;2=>National ID');
            $table->string('document_no')->nullable();
            $table->string('document_expire_date')->nullable();
            $table->string('document_issue_country')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_passangers');
    }
};
