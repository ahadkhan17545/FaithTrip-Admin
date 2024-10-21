<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlyhubFlightTicketIssue extends Model
{
    use HasFactory;
    public static function issueTicket($flightBookingInfo){

        $bookingResponse = json_decode($flightBookingInfo->booking_response, true);
        $tracking_id = $bookingResponse['general']['tracking_id'];

        $postData = array(
            "member_id" => "1",
            "tracking_id" => (string) $tracking_id,
            "price_change_accepted" => (string) "no",
            "notes" => (string) "not required",
        );

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $flyhubGds->api_endpoint.'/flight/issue-ticket',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'apikey: '.$flyhubGds->api_key,
            'secretecode: '.$flyhubGds->secret_code,
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    public static function cancelTicket($flightBookingInfo){

        $bookingResponse = json_decode($flightBookingInfo->booking_response, true);
        $tracking_id = $bookingResponse['general']['tracking_id'];

        $postData = array(
            "member_id" => "1",
            "tracking_id" => (string) $tracking_id,
            "reason" => "Trip Cancelled",
        );

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $flyhubGds->api_endpoint.'/flight/cancel-booking',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'apikey: '.$flyhubGds->api_key,
            'secretecode: '.$flyhubGds->secret_code,
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }
}
