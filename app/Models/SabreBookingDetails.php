<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SabreBookingDetails extends Model
{
    use HasFactory;

    public static function getBookingDetails($pnrId){

        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){
            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");
            if($currentDate >= $tokenExpireDate){
                SabreFlightSearch::generateAccessToken();
            }
        } else {
            SabreFlightSearch::generateAccessToken();
        }

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v1/trip/orders/getBooking';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v1/trip/orders/getBooking';
        }

        $accessToken = session('access_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiEndPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "confirmationId" => (string) $pnrId
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Conversation-ID: 2021.01.DevStudio",
                "Authorization: Bearer $accessToken",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);

        $airlinePNRArray = [];
        $flightBookingInfo = FlightBooking::where('pnr_id', $pnrId)->where('gds', 'Sabre')->first();
        if(isset($responseData['flights'])){
            foreach($responseData['flights'] as $flightData){
                if(isset($flightData['confirmationId'])){
                    $airlinePNRArray[] = $flightData['confirmationId'];
                }
            }
            if(count($airlinePNRArray) > 0){
                $commaSeparated = implode(", ", $airlinePNRArray);
                $flightBookingInfo->airlines_pnr = $commaSeparated;
                $flightBookingInfo->save();
            }
        } else {
            if($flightBookingInfo->status == 1){
                $flightBookingInfo->status = 3;
                $flightBookingInfo->save();
            }
            if($flightBookingInfo->status == 2){
                $flightBookingInfo->status = 4;
                $flightBookingInfo->save();
            }
        }


    }
}
