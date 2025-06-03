<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightPassangerResource extends JsonResource
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
            'ticket_no' => $this->ticket_no,
            'type' => $this->passanger_type,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'age' => $this->age,
            'document' => [
                'type' => $this->document_type,
                'number' => $this->document_no,
                'expire_date' => $this->document_expire_date,
                'issue_country' => $this->document_issue_country
            ],
            'nationality' => $this->nationality,
            'frequent_flyer_no' => $this->frequent_flyer_no,
        ];
    }
}
