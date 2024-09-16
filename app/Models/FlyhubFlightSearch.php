<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlyhubFlightSearch extends Model
{
    use HasFactory;

    public static function getFlightSearchResults($originAirportCode, $destinationAirportCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $cabinClass, $preferredAirlinesArray){

        ini_set('max_execution_time', '600'); // 10 mins

        // Define your dynamic variables
        $journey_type = $flightType == 1 ? "OneWay" : "RoundTrip";
        $departure_airport = $originAirportCode;
        $arrival_airport = $destinationAirportCode;
        $departure_date = $departureDate;
        $travelers_adult = $adult;
        $travelers_child = $child;
        $travelers_child_age = 0;
        $travelers_infants = $infant;
        $travelers_infants_age = [""];

        $preferred_carrier = [""];
        if(count($preferredAirlinesArray) > 0){
            $preferred_carrier = $preferredAirlinesArray;
        }

        $non_stop_flight = "any";
        $baggage_option = "any";
        $booking_class = $cabinClass;
        $supplier_uid = "all";
        $partner_id = "1";
        $language = "en";


        $segmentArray = [];
        if($flightType == 1){ //oneway
            $segmentArray[] = [
                "departure_airport_type" => "AIRPORT", // CITY or AIRPORT
                "departure_airport" => $departure_airport,
                "arrival_airport_type" => "AIRPORT", // CITY or AIRPORT
                "arrival_airport" => $arrival_airport,
                "departure_date" => $departure_date
            ];
        } else { //roundtrip
            $segmentArray[] = [
                "departure_airport_type" => "AIRPORT", // CITY or AIRPORT
                "departure_airport" => $departure_airport,
                "arrival_airport_type" => "AIRPORT", // CITY or AIRPORT
                "arrival_airport" => $arrival_airport,
                "departure_date" => $departure_date,
                "arrival_date" => $returnDate
            ];
        }

        // Create the data array
        $data = [
            "journey_type" => $journey_type,
            "segment" => $segmentArray,
            "travelers_adult" => $travelers_adult,
            "travelers_child" => $travelers_child,
            "travelers_child_age" => $travelers_child_age,
            "travelers_infants" => $travelers_infants,
            "travelers_infants_age" => $travelers_infants_age,
            "preferred_carrier" => $preferred_carrier,
            "non_stop_flight" => $non_stop_flight,
            "baggage_option" => $baggage_option,
            "booking_class" => $booking_class,
            "supplier_uid" => $supplier_uid,
            "partner_id" => $partner_id,
            "language" => $language,
        ];

        // Convert the data array to JSON
        $json_data = json_encode($data);

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        // Initialize cURL
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $flyhubGds->api_endpoint."/flight/search",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'apikey: '.$flyhubGds->api_key,
                'secretecode: '.$flyhubGds->secret_code,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $rawSearchResults = json_decode($response, true);

        // custom data set
        $searchResults = array();
        if(isset($rawSearchResults['status']) && $rawSearchResults['status'] == 'success'){
            foreach($rawSearchResults['data'] as $index => $item){

                $searchResults[$index]['journey_type'] = $flightType; //1=>oneway; 2=>Roundtrip

                // departure
                $searchResults[$index]['departure_datetime'] = $item['flight_group'][0]['routes'][0]['departure_time']; //"2024-06-30T18:45:00.000+06:00"
                $searchResults[$index]['departure_city_code'] = null;
                $searchResults[$index]['departure_city_name'] = $item['flight_group'][0]['routes'][0]['origin_airport']['city'];
                $searchResults[$index]['departure_airport_code'] = $item['flight_group'][0]['routes'][0]['origin'];
                $searchResults[$index]['departure_airport_name'] = $item['flight_group'][0]['routes'][0]['origin_airport']['name'];
                $searchResults[$index]['departure_terminal'] = $item['flight_group'][0]['routes'][0]['origin_terminal'];
                $searchResults[$index]['departure_country_code'] = null;
                $searchResults[$index]['departure_country_name'] = $item['flight_group'][0]['routes'][0]['origin_airport']['country'];

                // arrival
                $searchResults[$index]['arrival_datetime'] = end($item['flight_group'][0]['routes'])['arrival_time']; //"2024-06-30T18:45:00.000+06:00"
                $searchResults[$index]['arrival_city_code'] = null;
                $searchResults[$index]['arrival_city_name'] = end($item['flight_group'][0]['routes'])['destination_airport']['city'];
                $searchResults[$index]['arrival_airport_code'] = end($item['flight_group'][0]['routes'])['destination'];
                $searchResults[$index]['arrival_airport_name'] = end($item['flight_group'][0]['routes'])['destination_airport']['name'];
                $searchResults[$index]['arrival_terminal'] = end($item['flight_group'][0]['routes'])['destination_terminal'];
                $searchResults[$index]['arrival_country_code'] = null;
                $searchResults[$index]['arrival_country_name'] = end($item['flight_group'][0]['routes'])['destination_airport']['country'];

                // carrier info (for segment-1 only if mutiple segments are there)
                $searchResults[$index]['operating_carrier_code'] = $item['flight_group'][0]['routes'][0]['operating']['carrier'];
                $searchResults[$index]['operating_carrier_name'] = $item['flight_group'][0]['routes'][0]['operating']['carrier_name'];
                $searchResults[$index]['operating_flight_number'] = $item['flight_group'][0]['routes'][0]['operating']['flight_number'];
                $searchResults[$index]['marketing_carrier_code'] = $item['flight_group'][0]['routes'][0]['marketing']['carrier'];
                $searchResults[$index]['marketing_carrier_name'] = $item['flight_group'][0]['routes'][0]['marketing']['carrier_name'];
                $searchResults[$index]['marketing_flight_number'] = $item['flight_group'][0]['routes'][0]['marketing']['flight_number'];

                // others info
                $searchResults[$index]['total_elapsed_time'] = $item['flight_group'][0]['flight_time'];
                $searchResults[$index]['total_miles_flown'] = null;
                $searchResults[$index]['last_ticket_datetime'] = $item['last_ticket_time']; //"2024-06-30T23:59:00.000+05:30" or null

                // refund info
                $searchResults[$index]['refundable'] = $item['fare_rules']['refundable'];
                $searchResults[$index]['change_before_departure'] = $item['fare_rules']['change_before_departure'];
                $searchResults[$index]['penalty'] = isset($item['fare_rules']['refundable_data']) && isset($item['fare_rules']['refundable_data'][0]['Amount'][0]) ? $item['fare_rules']['refundable_data'][0]['Amount'][0] : null; //BDT5703
                $searchResults[$index]['penalty_applicable'] = isset($item['fare_rules']['refundable_data']) && isset($item['fare_rules']['refundable_data'][0]['PenaltyApplies']) ? $item['fare_rules']['refundable_data'][0]['PenaltyApplies'] : null;

                // pricing
                $searchResults[$index]['base_fare_amount'] = $item['price']['base_fare']['amount'];
                $searchResults[$index]['total_tax_amount'] = $item['price']['tax']['amount'];
                $searchResults[$index]['total_fare'] = $item['price']['total']['amount'];
                $searchResults[$index]['currency'] = $item['price']['total']['currency'];

                // segments start
                $segmentsArray = array();
                foreach($item['flight_group'][0]['routes'] as $segmentIndex => $route){

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
                // segments end

                $searchResults[$index]['segments'] = $segmentsArray;
            }
        }
        // custom data set

        return $searchResults;
    }
}
