<?php

namespace App\Models;

use DateTime;
use DateTimeZone;
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
            $flightBookingInfo->get_booking_response = $response;
        }


        // code for last ticketing deadline from get booking response start
        $specialServiceADTKText = null;
        $specialServiceOTHSText = [];
        if (!empty($responseData['specialServices']) && is_array($responseData['specialServices'])) {
            foreach ($responseData['specialServices'] as $service) {
                if (isset($service['code']) && $service['code'] === 'ADTK') {
                    $specialServiceADTKText = $service['message'];
                    break;
                }
                if (isset($service['code']) && $service['code'] === 'OTHS') {
                    $specialServiceOTHSText[] = $service['message'];
                }
            }

            if($specialServiceADTKText){

                $year = date("Y", strtotime($flightBookingInfo->departure_date));

                // Pattern 1: e.g., "04JUN 2300"
                if (preg_match('/\b(\d{2})([A-Z]{3})\s(\d{4})\b/', $specialServiceADTKText, $matches)) {
                    $day = $matches[1];
                    $monthStr = $matches[2];
                    $timeStr = $matches[3];
                    $hour = substr($timeStr, 0, 2);
                    $minute = substr($timeStr, 2, 2);
                    $dateString = "$day$monthStr$year $hour:$minute";
                    $date = DateTime::createFromFormat('dMY H:i', $dateString);

                // Pattern 2: e.g., "11AUG25 AT 1537"
                } elseif (preg_match('/\b(\d{2})([A-Z]{3})(\d{2})\s+AT\s+(\d{4})\b/i', $specialServiceADTKText, $matches)) {
                    $day = $matches[1];
                    $monthStr = strtoupper($matches[2]);
                    $year = 2000 + (int)$matches[3]; // Convert "25" to 2025
                    $timeStr = $matches[4];
                    $hour = substr($timeStr, 0, 2);
                    $minute = substr($timeStr, 2, 2);
                    $dateString = "$day$monthStr$year $hour:$minute";
                    $date = DateTime::createFromFormat('dMY H:i', $dateString);
                }

                // If a date was successfully parsed, save it
                if (!empty($date)) {
                    $flightBookingInfo->last_ticket_datetime = $date->format('Y-m-d H:i').":00";
                }

            } else {

                // Month‐abbr → month‐number map
                $monthMap = [
                    'JAN'=>1,'FEB'=>2,'MAR'=>3,'APR'=>4,'MAY'=>5,'JUN'=>6,
                    'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12,
                ];

                foreach ($specialServiceOTHSText as $r) {
                    // look for “BY DDMMMYY HHMMGMT”
                    // if (preg_match('/BY\s+(\d{2}[A-Z]{3}\d{2})\s+(\d{4})GMT/i', $r, $m)) {
                    if (preg_match('/BY\s+(\d{2}[A-Z]{3}\d{2})\s+(\d{4})(?=\D|$)/i', $r, $m)) {
                        // $m[1] = e.g. “07AUG25”, $m[2] = “1759”
                        $rawDate = $m[1];
                        $rawTime = $m[2];

                        // parse out day, month‐abbr, two‐digit year
                        $day      = (int)substr($rawDate, 0, 2);
                        $monAbbr  = strtoupper(substr($rawDate, 2, 3));
                        $year2    = (int)substr($rawDate, 5, 2);

                        // resolve to full year (e.g. “25” → 2025; adjust logic if you need a different pivot)
                        $year = 2000 + $year2;

                        // if (!isset($monthMap[$monAbbr])) {
                        //     throw new \Exception("Unknown month abbreviation “{$monAbbr}”");
                        // }
                        $month = $monthMap[$monAbbr];

                        // format time HHMM → HH:MM
                        $hour   = substr($rawTime, 0, 2);
                        $minute = substr($rawTime, 2, 2);

                        // build a DateTime in GMT
                        $dt = DateTime::createFromFormat(
                            'Y-n-j H:i',
                            sprintf('%04d-%d-%d %02d:%02d', $year, $month, $day, $hour, $minute),
                            new DateTimeZone('GMT')
                        );
                        // if (!$dt) {
                        //     throw new \Exception("Failed to parse date/time");
                        // }

                        // (optional) convert to your local zone:
                        // $dt->setTimezone(new DateTimeZone('Europe/Berlin'));

                        // store and break
                        $flightBookingInfo->last_ticket_datetime = $dt->format('Y-m-d H:i').":00";
                        break;
                    }
                }


            }
        }
        // code for last ticketing deadline from get booking response end


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

        SabreBookingDetails::updateClassAndBaggage($flightBookingInfo->id, $responseData);
        $flightBookingInfo->save();

        // update ticket no if flight is ticketed
        if($flightBookingInfo->status == 2){
            $flightPassangers = FlightPassanger::where('flight_booking_id', $flightBookingInfo->id)->get();
            if(isset($responseData['flightTickets']) && count($responseData['flightTickets']) > 0){
                $flightPassangerIndex = 0;
                foreach($flightPassangers as $flightPassanger){
                    FlightPassanger::where('id', $flightPassanger->id)->update([
                        'ticket_no' => $responseData['flightTickets'][$flightPassangerIndex]['number'] ?? null
                    ]);
                    $flightPassangerIndex++;
                }
            }
        }
        // update ticket no if flight is ticketed

    }

    public static function updateClassAndBaggage($flightBookingId, $getBookingResponse){

        $data = $getBookingResponse;
        $perSegmentBaggage = [];

        // 4. Initialize $perSegmentBaggage with each segment’s basic info
        if(isset($data['flights'])){
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
            foreach($flightSegments as $flightSegment){
                FlightSegment::where('id', $flightSegment->id)->update([
                    'baggage_allowance' => $customBaggageArray[$customIndex]['checkedBaggage'],
                    'cabin_baggage' => $customBaggageArray[$customIndex]['cabinBaggage'],
                    'booking_code' => $data['flights'][$customIndex]['bookingClass'] ?? null,
                    'cabin_code' => $data['flights'][$customIndex]['cabinTypeCode'] ?? null,
                ]);
                $customIndex++;
            }

        }
    }
}
