<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightSegmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'total_miles_flown' => $this->total_miles_flown,
            'elapsed_time' => $this->elapsed_time,
            'booking_code' => $this->booking_code,
            'cabin_code' => $this->cabin_code,
            'baggage_allowance' => $this->baggage_allowance,
            'departure' => [
                'airport' => $this->departure_airport_code,
                'city' => $this->departure_city_code,
                'country' => $this->departure_country_code,
                'time' => $this->departure_time,
                'terminal' => $this->departure_terminal
            ],
            'arrival' => [
                'airport' => $this->arrival_airport_code,
                'city' => $this->arrival_city_code,
                'country' => $this->arrival_country_code,
                'time' => $this->arrival_time,
                'terminal' => $this->arrival_terminal
            ],
            'carrier' => [
                'marketing' => $this->carrier_marketing_code,
                'marketing_flight_number' => $this->carrier_marketing_flight_number,
                'operating' => $this->carrier_operating_code,
                'operating_flight_number' => $this->carrier_operating_flight_number,
                'equipment_code' => $this->carrier_equipment_code
            ],
        ];
    }
}
