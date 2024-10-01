<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlyhubFlightRevalidate extends Model
{
    use HasFactory;

    public static function flightRevalidate($sessionIndex){
        $revalidatedResult = session('search_results');
        $data = $revalidatedResult[$sessionIndex];

        $postFields = array(
            "member_id" => "1",
            "tracking_id" => $data['flyhub_tracking_id'],
            "flight_key" => $data['flyhub_flight_key'],
            "result_type" => "general"
        );

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $flyhubGds->api_endpoint."/flight/validate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'apikey: '.$flyhubGds->api_key,
                'secretecode: '.$flyhubGds->secret_code,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);


        // echo $response;
        // exit();


        $rawValidatedResult = json_decode($response, true);
        // custom data set
        $revalidatedResult = array();
        if(isset($rawValidatedResult['data']) && $rawValidatedResult['status'] == 'success'){

            $revalidatedResult['journey_type'] = session('flight_type'); //1=>oneway; 2=>Roundtrip
            $revalidatedResult['flyhub_booking_tracking_id'] = $rawValidatedResult['booking_tracking_id']; //only for flyhub
            $revalidatedResult['flyhub_tracking_id'] = $rawValidatedResult['data']['tracking_id']; //only for flyhub
            $revalidatedResult['flyhub_flight_key'] = $rawValidatedResult['data']['flight_key']; //only for flyhub
            $revalidatedResult['session_expired_at'] = $rawValidatedResult['session_expire']['datetime']; //only for flyhub revalidate

            // departure
            $revalidatedResult['departure_datetime'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['departure_time']; //"2024-06-30T18:45:00.000+06:00"
            $revalidatedResult['departure_city_code'] = null;
            $revalidatedResult['departure_city_name'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['origin_airport']['city'];
            $revalidatedResult['departure_airport_code'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['origin'];
            $revalidatedResult['departure_airport_name'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['origin_airport']['name'];
            $revalidatedResult['departure_terminal'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['origin_terminal'];
            $revalidatedResult['departure_country_code'] = null;
            $revalidatedResult['departure_country_name'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['origin_airport']['country'];

            // arrival
            $revalidatedResult['arrival_datetime'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['arrival_time']; //"2024-06-30T18:45:00.000+06:00"
            $revalidatedResult['arrival_city_code'] = null;
            $revalidatedResult['arrival_city_name'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['destination_airport']['city'];
            $revalidatedResult['arrival_airport_code'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['destination'];
            $revalidatedResult['arrival_airport_name'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['destination_airport']['name'];
            $revalidatedResult['arrival_terminal'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['destination_terminal'];
            $revalidatedResult['arrival_country_code'] = null;
            $revalidatedResult['arrival_country_name'] = end($rawValidatedResult['data']['flight_group'][0]['routes'])['destination_airport']['country'];

            // carrier info (for segment-1 only if mutiple segments are there)
            $revalidatedResult['operating_carrier_code'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['operating']['carrier'];
            $revalidatedResult['operating_carrier_name'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['operating']['carrier_name'];
            $revalidatedResult['operating_flight_number'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['operating']['flight_number'];
            $revalidatedResult['marketing_carrier_code'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['marketing']['carrier'];
            $revalidatedResult['marketing_carrier_name'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['marketing']['carrier_name'];
            $revalidatedResult['marketing_flight_number'] = $rawValidatedResult['data']['flight_group'][0]['routes'][0]['marketing']['flight_number'];

            // for return flights
            if(isset($rawValidatedResult['data']['flight_group'][1])){

                // return departure
                $revalidatedResult['return_departure_datetime'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['departure_time']; //"2024-06-30T18:45:00.000+06:00"
                $revalidatedResult['return_departure_city_code'] = null;
                $revalidatedResult['return_departure_city_name'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['origin_airport']['city'];
                $revalidatedResult['return_departure_airport_code'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['origin'];
                $revalidatedResult['return_departure_airport_name'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['origin_airport']['name'];
                $revalidatedResult['return_departure_terminal'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['origin_terminal'];
                $revalidatedResult['return_departure_country_code'] = null;
                $revalidatedResult['return_departure_country_name'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['origin_airport']['country'];

                // arrival
                $revalidatedResult['return_arrival_datetime'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['arrival_time']; //"2024-06-30T18:45:00.000+06:00"
                $revalidatedResult['return_arrival_city_code'] = null;
                $revalidatedResult['return_arrival_city_name'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['destination_airport']['city'];
                $revalidatedResult['return_arrival_airport_code'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['destination'];
                $revalidatedResult['return_arrival_airport_name'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['destination_airport']['name'];
                $revalidatedResult['return_arrival_terminal'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['destination_terminal'];
                $revalidatedResult['return_arrival_country_code'] = null;
                $revalidatedResult['return_arrival_country_name'] = end($rawValidatedResult['data']['flight_group'][1]['routes'])['destination_airport']['country'];

                // carrier info (for segment-1 only if mutiple segments are there)
                $revalidatedResult['return_operating_carrier_code'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['operating']['carrier'];
                $revalidatedResult['return_operating_carrier_name'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['operating']['carrier_name'];
                $revalidatedResult['return_operating_flight_number'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['operating']['flight_number'];
                $revalidatedResult['return_marketing_carrier_code'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['marketing']['carrier'];
                $revalidatedResult['return_marketing_carrier_name'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['marketing']['carrier_name'];
                $revalidatedResult['return_marketing_flight_number'] = $rawValidatedResult['data']['flight_group'][1]['routes'][0]['marketing']['flight_number'];

            }

            // others info
            $revalidatedResult['onward_total_elapsed_time'] = $rawValidatedResult['data']['flight_group'][0]['flight_time'];
            $revalidatedResult['onward_stops'] = $rawValidatedResult['data']['flight_group'][0]['no_of_stops'];
            $revalidatedResult['return_total_elapsed_time'] = isset($rawValidatedResult['data']['flight_group'][1]) ? $rawValidatedResult['data']['flight_group'][1]['flight_time'] : null;
            $revalidatedResult['return_stops'] = isset($rawValidatedResult['data']['flight_group'][1]) ? $rawValidatedResult['data']['flight_group'][1]['no_of_stops'] : null;
            $revalidatedResult['total_miles_flown'] = null;
            $revalidatedResult['last_ticket_datetime'] = $rawValidatedResult['data']['last_ticket_time']; //"2024-06-30T23:59:00.000+05:30" or null

            // refund info
            $revalidatedResult['refundable'] = $rawValidatedResult['data']['fare_rules']['refundable'];
            $revalidatedResult['change_before_departure'] = $rawValidatedResult['data']['fare_rules']['change_before_departure'];
            $revalidatedResult['penalty'] = isset($rawValidatedResult['data']['fare_rules']['refundable_data']) && isset($rawValidatedResult['data']['fare_rules']['refundable_data'][0]['Amount'][0]) ? $rawValidatedResult['data']['fare_rules']['refundable_data'][0]['Amount'][0] : null; //BDT5703
            $revalidatedResult['penalty_applicable'] = isset($rawValidatedResult['data']['fare_rules']['refundable_data']) && isset($rawValidatedResult['data']['fare_rules']['refundable_data'][0]['PenaltyApplies']) ? $rawValidatedResult['data']['fare_rules']['refundable_data'][0]['PenaltyApplies'] : null;

            // pricing
            if(isset($rawValidatedResult['data']['margin'])){
                $revalidatedResult['base_fare_amount'] = $rawValidatedResult['data']['margin']['supplier']['base_fare']['amount'];
                $revalidatedResult['total_tax_amount'] = $rawValidatedResult['data']['margin']['supplier']['tax']['amount'];
                $revalidatedResult['total_fare'] = $rawValidatedResult['data']['margin']['supplier']['total']['amount'];
                $revalidatedResult['currency'] = $rawValidatedResult['data']['margin']['supplier']['total']['currency'];
            } else {
                $revalidatedResult['base_fare_amount'] = $rawValidatedResult['data']['price']['supplier']['base_fare'];
                $revalidatedResult['total_tax_amount'] = $rawValidatedResult['data']['price']['supplier']['tax'];
                $revalidatedResult['total_fare'] = $rawValidatedResult['data']['price']['supplier']['total'];
                $revalidatedResult['currency'] = $rawValidatedResult['data']['price']['supplier']['currency'];
            }


            // onward segments start
            $segmentsArray = array();
            foreach($rawValidatedResult['data']['flight_group'][0]['routes'] as $segmentIndex => $route){

                $segmentsArray[$segmentIndex]['departure_datetime'] = $route['departure_time'];
                $segmentsArray[$segmentIndex]['departure_city_code'] = null;
                $segmentsArray[$segmentIndex]['departure_city_name'] = $route['origin_airport']['city'];
                $segmentsArray[$segmentIndex]['departure_airport_code'] = $route['origin'];
                $segmentsArray[$segmentIndex]['departure_airport_name'] = $route['origin_airport']['name'];
                $segmentsArray[$segmentIndex]['departure_terminal'] = $route['origin_terminal'];
                $segmentsArray[$segmentIndex]['departure_country_code'] = null;
                $segmentsArray[$segmentIndex]['arrival_country_name'] = $route['origin_airport']['country'];

                $segmentsArray[$segmentIndex]['arrival_datetime'] = $route['arrival_time'];
                $segmentsArray[$segmentIndex]['arrival_city_code'] = null;
                $segmentsArray[$segmentIndex]['arrival_city_name'] = $route['destination_airport']['city'];
                $segmentsArray[$segmentIndex]['arrival_airport_code'] = $route['destination'];
                $segmentsArray[$segmentIndex]['arrival_airport_name'] = $route['destination_airport']['name'];
                $segmentsArray[$segmentIndex]['arrival_terminal'] = $route['destination_terminal'];
                $segmentsArray[$segmentIndex]['arrival_country_code'] = null;
                $segmentsArray[$segmentIndex]['arrival_country_name'] = $route['destination_airport']['country'];

                $segmentsArray[$segmentIndex]['operating_carrier_code'] = $route['operating']['carrier'];
                $segmentsArray[$segmentIndex]['operating_carrier_name'] = $route['operating']['carrier_name'];
                $segmentsArray[$segmentIndex]['operating_flight_number'] = $route['operating']['flight_number'];
                $segmentsArray[$segmentIndex]['marketing_carrier_code'] = $route['marketing']['carrier'];
                $segmentsArray[$segmentIndex]['marketing_carrier_name'] = $route['marketing']['carrier_name'];
                $segmentsArray[$segmentIndex]['marketing_flight_number'] = $route['marketing']['flight_number'];

                $segmentsArray[$segmentIndex]['available_seats'] = $route['booking_class']['seat_available'];
                $segmentsArray[$segmentIndex]['miles_flown'] = $route['distance'];
                $segmentsArray[$segmentIndex]['elapsed_time'] = $route['flight_time'];
                $segmentsArray[$segmentIndex]['cabin_class'] = $route['booking_class']['cabin_class'];
                $segmentsArray[$segmentIndex]['cabin_code'] = $route['booking_class']['cabin_code'];
                $segmentsArray[$segmentIndex]['booking_code'] = $route['booking_class']['booking_code'];
                $segmentsArray[$segmentIndex]['meal_code'] = $route['booking_class']['meal_code'];
                $segmentsArray[$segmentIndex]['baggage_allowance'] = $route['baggages']; //includes adult child infant dynamically based on search

            }
            $revalidatedResult['segments'] = $segmentsArray;
            // onward segments end


            // return segments start
            $segmentsArray = array();
            if(isset($rawValidatedResult['data']['flight_group'][1]['routes'])){
                foreach($rawValidatedResult['data']['flight_group'][1]['routes'] as $returnSegmentIndex => $route){

                    $segmentsArray[$returnSegmentIndex]['departure_datetime'] = $route['departure_time'];
                    $segmentsArray[$returnSegmentIndex]['departure_city_code'] = null;
                    $segmentsArray[$returnSegmentIndex]['departure_city_name'] = $route['origin_airport']['city'];
                    $segmentsArray[$returnSegmentIndex]['departure_airport_code'] = $route['origin'];
                    $segmentsArray[$returnSegmentIndex]['departure_airport_name'] = $route['origin_airport']['name'];
                    $segmentsArray[$returnSegmentIndex]['departure_terminal'] = $route['origin_terminal'];
                    $segmentsArray[$returnSegmentIndex]['departure_country_code'] = null;
                    $segmentsArray[$returnSegmentIndex]['arrival_country_name'] = $route['origin_airport']['country'];

                    $segmentsArray[$returnSegmentIndex]['arrival_datetime'] = $route['arrival_time'];
                    $segmentsArray[$returnSegmentIndex]['arrival_city_code'] = null;
                    $segmentsArray[$returnSegmentIndex]['arrival_city_name'] = $route['destination_airport']['city'];
                    $segmentsArray[$returnSegmentIndex]['arrival_airport_code'] = $route['destination'];
                    $segmentsArray[$returnSegmentIndex]['arrival_airport_name'] = $route['destination_airport']['name'];
                    $segmentsArray[$returnSegmentIndex]['arrival_terminal'] = $route['destination_terminal'];
                    $segmentsArray[$returnSegmentIndex]['arrival_country_code'] = null;
                    $segmentsArray[$returnSegmentIndex]['arrival_country_name'] = $route['destination_airport']['country'];

                    $segmentsArray[$returnSegmentIndex]['operating_carrier_code'] = $route['operating']['carrier'];
                    $segmentsArray[$returnSegmentIndex]['operating_carrier_name'] = $route['operating']['carrier_name'];
                    $segmentsArray[$returnSegmentIndex]['operating_flight_number'] = $route['operating']['flight_number'];
                    $segmentsArray[$returnSegmentIndex]['marketing_carrier_code'] = $route['marketing']['carrier'];
                    $segmentsArray[$returnSegmentIndex]['marketing_carrier_name'] = $route['marketing']['carrier_name'];
                    $segmentsArray[$returnSegmentIndex]['marketing_flight_number'] = $route['marketing']['flight_number'];

                    $segmentsArray[$returnSegmentIndex]['available_seats'] = $route['booking_class']['seat_available'];
                    $segmentsArray[$returnSegmentIndex]['miles_flown'] = $route['distance'];
                    $segmentsArray[$returnSegmentIndex]['elapsed_time'] = $route['flight_time'];
                    $segmentsArray[$returnSegmentIndex]['cabin_class'] = $route['booking_class']['cabin_class'];
                    $segmentsArray[$returnSegmentIndex]['cabin_code'] = $route['booking_class']['cabin_code'];
                    $segmentsArray[$returnSegmentIndex]['booking_code'] = $route['booking_class']['booking_code'];
                    $segmentsArray[$returnSegmentIndex]['meal_code'] = $route['booking_class']['meal_code'];
                    $segmentsArray[$returnSegmentIndex]['baggage_allowance'] = $route['baggages']; //includes adult child infant dynamically based on search

                }
                $revalidatedResult['return_segments'] = $segmentsArray;
            }
            // return segments end

        }
        // custom data set

        return $revalidatedResult;
    }
}
