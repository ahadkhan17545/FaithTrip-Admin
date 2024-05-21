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
        Schema::create('flight_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flight_booking_id')->nullable()->comment('FlightBooking Table Id');

            $table->string('total_miles_flown')->nullable();
            $table->string('elapsed_time')->nullable();
            $table->string('booking_code')->nullable();
            $table->string('cabin_code')->nullable();
            $table->string('baggage_allowance')->nullable();

            $table->string('departure_airport_code')->nullable();
            $table->string('departure_city_code')->nullable();
            $table->string('departure_country_code')->nullable();
            $table->string('departure_time')->nullable();
            $table->string('departure_terminal')->nullable();

            $table->string('arrival_airport_code')->nullable();
            $table->string('arrival_city_code')->nullable();
            $table->string('arrival_country_code')->nullable();
            $table->string('arrival_time')->nullable();
            $table->string('arrival_terminal')->nullable();

            $table->string('carrier_marketing_code')->nullable();
            $table->string('carrier_marketing_flight_number')->nullable();
            $table->string('carrier_operating_code')->nullable();
            $table->string('carrier_operating_flight_number')->nullable();
            $table->string('carrier_equipment_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segments');
    }
};
