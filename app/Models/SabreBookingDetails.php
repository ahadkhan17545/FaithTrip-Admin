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

        if(isset($responseData['bookingId'])){
            $flightBookingInfo->booking_id = $responseData['bookingId'];
            if($flightBookingInfo->get_booking_response == null){
                $flightBookingInfo->get_booking_response = $response;
            }
        }


        // code for last ticketing deadline start
        $specialServiceADTKText = null;
        if (!empty($responseData['specialServices']) && is_array($responseData['specialServices'])) {
            foreach ($responseData['specialServices'] as $service) {
                if (isset($service['code']) && $service['code'] === 'ADTK') {
                    $specialServiceADTKText = $service['message'];
                    break;
                }
            }
        }

        if($specialServiceADTKText){
            if (preg_match('/\b(\d{2})([A-Z]{3})\s(\d{4})\b/', $specialServiceADTKText, $matches)) {

                $day = $matches[1];       // 04
                $monthStr = $matches[2];  // JUN
                $timeStr = $matches[3];   // 2300
                $year = date("Y");             // You can make this dynamic if needed

                // Convert 2300 to HH:MM format
                $hour = substr($timeStr, 0, 2);
                $minute = substr($timeStr, 2, 2);
                $dateString = "$day$monthStr$year $hour:$minute";
                $date = DateTime::createFromFormat('dMY H:i', $dateString);

                if ($date) {
                    $flightBookingInfo->last_ticket_datetime = $date->format('Y-m-d H:i');
                }
            }
        }
        // code for last ticketing deadline end


        if(isset($responseData['flights'])){
            foreach($responseData['flights'] as $flightData){
                if(isset($flightData['confirmationId'])){
                    $airlinePNRArray[] = $flightData['confirmationId'];
                }
            }
            if(count($airlinePNRArray) > 0){
                $commaSeparated = implode(", ", $airlinePNRArray);
                $flightBookingInfo->airlines_pnr = $commaSeparated;
            }
        } else {
            if($flightBookingInfo->status == 1){
                $flightBookingInfo->status = 3;
            }
            if($flightBookingInfo->status == 2){
                $flightBookingInfo->status = 4;
            }
        }

        SabreBookingDetails::fixBaggageAllowance($flightBookingInfo->id, $responseData);
        $flightBookingInfo->save();

    }

    public static function fixBaggageAllowance($flightBookingId, $getBookingResponse){

        $data = $getBookingResponse;
        $perSegmentBaggage = [];

        // 4. Initialize $perSegmentBaggage with each segment’s basic info
        foreach ($data['flights'] as $flight) {
            $segmentId = $flight['itemId'];
            $perSegmentBaggage[$segmentId] = [
                'segmentInfo' => [
                    'from'         => $flight['fromAirportCode']   ?? null,
                    'to'           => $flight['toAirportCode']     ?? null,
                    'departure'    => ($flight['departureDate'] ?? '') . ' ' . ($flight['departureTime'] ?? ''),
                    'arrival'      => ($flight['arrivalDate']   ?? '') . ' ' . ($flight['arrivalTime']   ?? ''),
                    'flightNumber' => ($flight['airlineCode']   ?? '') . ' ' . ($flight['flightNumber'] ?? ''),
                ],
                'cabinBaggage'   => [], // will hold cabin-baggage‐entries
                'checkedBaggage' => [], // will hold checked-baggage‐entries
            ];
        }

        // 5. Walk through every fareOffer; for each one, loop its "flights" array properly.
        if (isset($data['fareOffers']) && is_array($data['fareOffers'])) {
            foreach ($data['fareOffers'] as $fareOffer) {
                // Skip if no "flights" key or it's not an array
                if (!isset($fareOffer['flights']) || !is_array($fareOffer['flights'])) {
                    continue;
                }

                foreach ($fareOffer['flights'] as $flightRef) {
                    // Extract the actual segment‐ID string/int:
                    if (is_array($flightRef) && isset($flightRef['itemId'])) {
                        $coveredId = $flightRef['itemId'];
                    }
                    elseif (is_scalar($flightRef)) {
                        // In case it ever appears as ["8","9"] instead of [{itemId:"8"},…]
                        $coveredId = (string)$flightRef;
                    }
                    else {
                        // Unknown format: skip
                        continue;
                    }

                    // Now we have a scalar key. Safely check if it’s a known segment:
                    if (!array_key_exists($coveredId, $perSegmentBaggage)) {
                        // maybe a mismatch in ID‐naming; just skip
                        continue;
                    }

                    // 5a. Cabin baggage: collect either allowance or charges (if present)
                    if (isset($fareOffer['cabinBaggageAllowance'])) {
                        $perSegmentBaggage[$coveredId]['cabinBaggage'][] = [
                            'type'    => 'allowance',
                            'details' => $fareOffer['cabinBaggageAllowance'],
                        ];
                    }
                    if (isset($fareOffer['cabinBaggageCharges'])) {
                        $perSegmentBaggage[$coveredId]['cabinBaggage'][] = [
                            'type'    => 'charges',
                            'details' => $fareOffer['cabinBaggageCharges'],
                        ];
                    }

                    // 5b. Checked baggage: collect either allowance or charges (if present)
                    if (isset($fareOffer['checkedBaggageAllowance'])) {
                        $perSegmentBaggage[$coveredId]['checkedBaggage'][] = [
                            'type'    => 'allowance',
                            'details' => $fareOffer['checkedBaggageAllowance'],
                        ];
                    }
                    if (isset($fareOffer['checkedBaggageCharges'])) {
                        $perSegmentBaggage[$coveredId]['checkedBaggage'][] = [
                            'type'    => 'charges',
                            'details' => $fareOffer['checkedBaggageCharges'],
                        ];
                    }
                }
            }

            $customBaggageArray = [];
            $customIndex = 0;
            foreach($perSegmentBaggage as $segmentBaggage){
                $cabinBaggage = '';
                if(isset($segmentBaggage['cabinBaggage'][0]['details']['totalWeightInKilograms'])){
                    $cabinBaggage = $segmentBaggage['cabinBaggage'][0]['details']['totalWeightInKilograms']."kg";
                } else {
                    $cabinBaggage = $segmentBaggage['cabinBaggage'][0]['details']['baggagePieces'][0]['maximumWeightInKilograms']."kg";
                }
                if(isset($segmentBaggage['cabinBaggage'][0]['details']['maximumPieces'])){
                    $cabinBaggage .= "*".$segmentBaggage['cabinBaggage'][0]['details']['maximumPieces'];
                }

                $checkedBaggage = '';
                if(isset($segmentBaggage['checkedBaggage'][0]['details']['totalWeightInKilograms'])){
                    $checkedBaggage = $segmentBaggage['checkedBaggage'][0]['details']['totalWeightInKilograms']."kg";
                } else {
                    $checkedBaggage = $segmentBaggage['checkedBaggage'][0]['details']['baggagePieces'][0]['maximumWeightInKilograms']."kg";
                }
                if(isset($segmentBaggage['checkedBaggage'][0]['details']['maximumPieces'])){
                    $checkedBaggage .= "*".$segmentBaggage['checkedBaggage'][0]['details']['maximumPieces'];
                }

                $customBaggageArray[$customIndex]['cabinBaggage'] = $cabinBaggage;
                $customBaggageArray[$customIndex]['checkedBaggage'] = $checkedBaggage;
                $customIndex++;
            }

            $flightSegments = FlightSegment::where('flight_booking_id', $flightBookingId)->get();
            $customIndex = 0;
            foreach($flightSegments as $flightSegmentIndex => $flightSegment){
                FlightSegment::where('id', $flightSegment->id)->update([
                    'baggage_allowance' => $customBaggageArray[$customIndex]['checkedBaggage'],
                    'cabin_baggage' => $customBaggageArray[$customIndex]['cabinBaggage'],
                ]);
                $customIndex++;
            }

        }
    }
}
