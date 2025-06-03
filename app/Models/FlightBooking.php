<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'transaction_id',
        'payment_status',
        'ticketing_response',
        'updated_at'
    ];
}
