<?php

namespace App\Http\Resources;

use App\Models\FlightPassanger;
use App\Models\FlightSegment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightBookingResource extends JsonResource
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
            'id' => $this->id,
            "source" => $this->source == 1 ? "Portal" : ($this->source == 2 ? "Website" : "Mobile App"),
            'passanger_id' => $this->passanger_id,
            'booking_no' => $this->booking_no,
            'pnr_id' => $this->pnr_id,
            "airlines_pnr" => $this->airlines_pnr,
            "booking_id" => $this->booking_id,
            "ticket_id" => $this->ticket_id,
            'traveller_name' => $this->traveller_name,
            'traveller_email' => $this->traveller_email,
            'traveller_contact' => $this->traveller_contact,
            'departure_date' => $this->departure_date,
            'departure_location' => $this->departure_location,
            'arrival_location' => $this->arrival_location,
            'governing_carriers' => $this->governing_carriers,
            'adult' => $this->adult,
            'child' => $this->child,
            'infant' => $this->infant,
            'base_fare_amount' => $this->base_fare_amount,
            'total_tax_amount' => $this->total_tax_amount,
            'total_fare' => $this->total_fare,
            'currency' => $this->currency,
            'last_ticket_datetime' => $this->last_ticket_datetime,
            'status' => (int) $this->status,

            'payment_status' => $this->payment_status == 0 ? "Pending" : ($this->payment_status == 1 ? "Success" : "Failed"),
            'payment_method' => $this->payment_method == 1 ? "SSLCommerz" : ($this->payment_method == 2 ? "bkash" : ($this->payment_method == 3 ? "bkash" : null)),
            'transaction_id' => $this->transaction_id,

            'is_live' => (int) $this->is_live,
            'created_at' => date("Y-m-d H:i:s", strtotime($this->created_at)),

            'segments' => FlightSegmentResource::collection(FlightSegment::where('flight_booking_id', $this->id)->get()),
            'passengers' => FlightPassangerResource::collection(FlightPassanger::where('flight_booking_id', $this->id)->get()),
        ];
    }
}
