<?php

namespace App\Http\Controllers;

use App\Models\SabreFlightRevalidate;
use App\Models\SabreGdsConfig;
use Illuminate\Http\Request;
use DateTime;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class FlightSearchController extends Controller
{
    public static function generateAccessToken(){

        // developer account of Fahim
        $authorizationHeader = base64_encode(base64_encode("V1:hxp6cy145bjv5hy9:DEVCENTER:EXT").':'.base64_encode("Hp8tT6iN"));
        $apiEndPoint = 'https://api.cert.platform.sabre.com/v2/auth/token';

        // Faithtrip account
        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $username = base64_encode($sabreGdsInfo->user_id);
            $password = base64_encode($sabreGdsInfo->password);
            $authorizationHeader = base64_encode($username.":".$password);
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v2/auth/token';
        } else{
            $username = base64_encode($sabreGdsInfo->production_user_id);
            $password = base64_encode($sabreGdsInfo->production_password);
            $authorizationHeader = base64_encode($username.":".$password);
            $apiEndPoint = 'https://api.platform.sabre.com/v2/auth/token';
        }

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
        CURLOPT_POSTFIELDS =>'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic '.$authorizationHeader,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        session(['access_token' => $accessToken]);
        session(['expires_in' => $expiresIn]);
    }

    public function getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs){

        // travellers info
        $passengerTypes = array();
        if ($adult > 0) {
            $passengerTypes[] = array("Code" => "ADT", "Quantity" => (int) $adult);
        }
        if ($child > 0) {
            $passengerTypes[] = array("Code" => "CNN", "Quantity" => (int) $child);
        }
        if ($infant > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (int) $infant);
        }

        $airTravelerAvail = [];
        $passengerTypeQuantity = [];
        foreach ($passengerTypes as $passengerType) {
            $passengerTypeQuantity[] = $passengerType;
        }
        $airTravelerAvail[] = array(
            "PassengerTypeQuantity" => $passengerTypeQuantity
        );

        // oneway or return flights
        $flightTypeData = array();
        if($flightType == 1){

            $flightTypeData[] = array_filter([
                "RPH" => "1",
                "DepartureDateTime" => "$departureDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

        } else {

            $flightTypeData[] = array_filter([
                "RPH" => "1",
                "DepartureDateTime" => "$departureDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

            $flightTypeData[] = array_filter([
                "RPH" => "2",
                "DepartureDateTime" => "$returnDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

        }

        // Sabre API request payload with dynamic query

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v5/offers/shop';
            $requestTypeName = "200ITINS";
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v5/offers/shop';
            $requestTypeName = "50ITINS";
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
            CURLOPT_POSTFIELDS => json_encode(array(
                "OTA_AirLowFareSearchRQ" => array(
                    "Version" => "5",
                    "POS" => array(
                        "Source" => array(
                            array(
                                "PseudoCityCode" => "S00L",
                                "RequestorID" => array(
                                    "Type" => "1",
                                    "ID" => "1",
                                    "CompanyName" => array(
                                        "Code" => "TN"
                                    )
                                )
                            )
                        )
                    ),
                    "OriginDestinationInformation" => $flightTypeData,
                    "TravelPreferences" => array(
                        "TPA_Extensions" => array(
                            "DataSources" => array(
                                "NDC" => "Disable",
                                "ATPCO" => "Enable",
                                "LCC" => "Disable"
                            ),
                            "PreferNDCSourceOnTie" => array(
                                "Value" => true
                            )
                        )
                    ),
                    "TravelerInfoSummary" => array(
                        "AirTravelerAvail" => $airTravelerAvail
                    ),
                    "TPA_Extensions" => array(
                        "IntelliSellTransaction" => array(
                            "RequestType" => array(
                                "Name" => $requestTypeName
                            )
                        )
                    )
                )
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Conversation-ID: ",
                "Authorization: Bearer $accessToken",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function searchFlights(Request $request){
        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){

            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");

            if($currentDate >= $tokenExpireDate){
                $this->generateAccessToken();
            }

        } else {
            $this->generateAccessToken();
        }

        $departureLocationId = $request->departure_location_id;
        $originCityInfo = DB::table('city_airports')->where('id', $departureLocationId)->first();
        $originCityCode = $originCityInfo->airport_code;
        $destinationLocationId = $request->destination_location_id;
        $destinationCityInfo = DB::table('city_airports')->where('id', $destinationLocationId)->first();
        $destinationCityCode = $destinationCityInfo->airport_code;
        $departureDate = date("Y-m-d", strtotime($request->departure_date));
        $returnDate = date("Y-m-d", strtotime($request->return_date));
        $adult = $request->adult;
        $child = $request->child;
        $infant = $request->infant;
        $flightType = $request->flight_type;


        // preferred airlines code start
        $preferredAirlines = $request->preferred_airlines; //comma separated id of airnline like 218,1359
        $airlinePrefs = null;

        if($preferredAirlines){
            $preferredAirlinesArray = [];
            foreach(explode(",",$preferredAirlines) as $preferredAirlineId){
                $airlineInfo = DB::table('airlines')->where('id', $preferredAirlineId)->first();
                if($airlineInfo && $airlineInfo->iata){
                    $preferredAirlinesArray[] = $airlineInfo->iata;
                }
            }
            $airlinePrefs = array_map(function($code) {
                return ["Code" => $code];
            }, $preferredAirlinesArray);
        }
        // preferred airlines code end


        // storing search query into session for modify search
        session([
            'departure_location_id' => $departureLocationId,
            'origin_city_name' => $originCityInfo->city_name,
            'destination_location_id' => $destinationLocationId,
            'destination_city_name' => $destinationCityInfo->city_name,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'adult' => $adult,
            'child' => $child,
            'infant' => $infant,
            'flight_type' => $flightType,
            'preferred_airlines' => $airlinePrefs,
        ]);

        $searchResults = $this->getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
        session(['search_results' => $searchResults]);

        // for carrier filters
        $searchResults = json_decode($searchResults, true);
        $operatingCodes = [];
        if(isset($searchResults['groupedItineraryResponse']['scheduleDescs'])){
            foreach ($searchResults['groupedItineraryResponse']['scheduleDescs'] as $schedule) {
                $operatingCodes[] = $schedule['carrier']['operating'];
            }
        }
        $operatingCodes = array_values(array_unique($operatingCodes));
        session(['search_results_operating_carriers' => $operatingCodes]);
        session()->forget('filter_min_price');
        session()->forget('filter_max_price');
        session()->forget('airline_carrier_code');
    }

    public function showFlightSearchResults(){
        $searchResults = json_decode(session('search_results'), true);
        $search_results_operating_carriers = session('search_results_operating_carriers');
        return view('flight.search_results', compact('searchResults', 'search_results_operating_carriers'));
    }

    public function searchNextDay(){
        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){

            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");

            if($currentDate >= $tokenExpireDate){
                $this->generateAccessToken();
            }

        } else {
            $this->generateAccessToken();
        }

        $departureLocationId = session('departure_location_id');
        $originCityInfo = DB::table('city_airports')->where('id', $departureLocationId)->first();
        $originCityCode = $originCityInfo->airport_code;

        $destinationLocationId = session('destination_location_id');
        $destinationCityInfo = DB::table('city_airports')->where('id', $destinationLocationId)->first();
        $destinationCityCode = $destinationCityInfo->airport_code;

        $departureDate = session('departure_date');
        $departureDate = date('Y-m-d', strtotime($departureDate . ' +1 day'));

        $returnDate = session('return_date');
        if($returnDate){
            $returnDate = date('Y-m-d', strtotime($returnDate . ' +1 day'));
        }

        $adult = session('adult');
        $child = session('child');
        $infant = session('infant');
        $flightType = session('flight_type');
        $airlinePrefs = session('preferred_airlines');

        // storing search query into session for modify search
        session([
            'departure_location_id' => $departureLocationId,
            'origin_city_name' => $originCityInfo->city_name,
            'destination_location_id' => $destinationLocationId,
            'destination_city_name' => $destinationCityInfo->city_name,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'adult' => $adult,
            'child' => $child,
            'infant' => $infant,
            'flight_type' => $flightType,
            'preferred_airlines' => $airlinePrefs,
        ]);

        $searchResults = $this->getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
        session(['search_results' => $searchResults]);

        // for carrier filters
        $searchResults = json_decode($searchResults, true);
        $operatingCodes = [];
        if(isset($searchResults['groupedItineraryResponse'])){
            foreach ($searchResults['groupedItineraryResponse']['scheduleDescs'] as $schedule) {
                $operatingCodes[] = $schedule['carrier']['operating'];
            }
        }
        $operatingCodes = array_values(array_unique($operatingCodes));
        session(['search_results_operating_carriers' => $operatingCodes]);
        session()->forget('filter_min_price');
        session()->forget('filter_max_price');
        session()->forget('airline_carrier_code');

        return redirect('flight/search-results');
    }

    public function searchPreviousDay(){
        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){

            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");

            if($currentDate >= $tokenExpireDate){
                $this->generateAccessToken();
            }

        } else {
            $this->generateAccessToken();
        }

        $departureLocationId = session('departure_location_id');
        $originCityInfo = DB::table('city_airports')->where('id', $departureLocationId)->first();
        $originCityCode = $originCityInfo->airport_code;

        $destinationLocationId = session('destination_location_id');
        $destinationCityInfo = DB::table('city_airports')->where('id', $destinationLocationId)->first();
        $destinationCityCode = $destinationCityInfo->airport_code;

        $departureDate = session('departure_date');
        $departureDate = date('Y-m-d', strtotime($departureDate . ' -1 day'));

        $returnDate = session('return_date');
        if($returnDate){
            $returnDate = date('Y-m-d', strtotime($returnDate . ' -1 day'));
        }

        $adult = session('adult');
        $child = session('child');
        $infant = session('infant');
        $flightType = session('flight_type');
        $airlinePrefs = session('preferred_airlines');

        // storing search query into session for modify search
        session([
            'departure_location_id' => $departureLocationId,
            'origin_city_name' => $originCityInfo->city_name,
            'destination_location_id' => $destinationLocationId,
            'destination_city_name' => $destinationCityInfo->city_name,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'adult' => $adult,
            'child' => $child,
            'infant' => $infant,
            'flight_type' => $flightType,
            'preferred_airlines' => $airlinePrefs,
        ]);

        $searchResults = $this->getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
        session(['search_results' => $searchResults]);

        // for carrier filters
        $searchResults = json_decode($searchResults, true);
        $operatingCodes = [];
        if(isset($searchResults['groupedItineraryResponse'])){
            foreach ($searchResults['groupedItineraryResponse']['scheduleDescs'] as $schedule) {
                $operatingCodes[] = $schedule['carrier']['operating'];
            }
        }
        $operatingCodes = array_values(array_unique($operatingCodes));
        session(['search_results_operating_carriers' => $operatingCodes]);
        session()->forget('filter_min_price');
        session()->forget('filter_max_price');
        session()->forget('airline_carrier_code');

        return redirect('flight/search-results');
    }

    public function priceRangeFilter(Request $request){
        if($request->min_price > 0){
            session(['filter_min_price' => $request->min_price]);
        }
        if($request->max_price > 0){
            session(['filter_max_price' => $request->max_price]);
        }
    }

    public function clearPriceRangeFilter(Request $request){
        session()->forget('filter_min_price');
        session()->forget('filter_max_price');

        Toastr::success('Filter Cleared', 'No Price Filter Added');
        return back();
    }

    public function airlineCarrierFilter(Request $request){

        if($request->type == 'add'){
            if(session('airline_carrier_code')){
                $airlineCarrierFilterArray = array();
                $airlineCarrierFilterArray = session('airline_carrier_code');
                if (!in_array($request->airline_carrier_code, $airlineCarrierFilterArray)) {
                    $airlineCarrierFilterArray[] = $request->airline_carrier_code;
                }
                session(['airline_carrier_code' => $airlineCarrierFilterArray]);
            } else {
                $airlineCarrierFilterArray = array();
                $airlineCarrierFilterArray[] = $request->airline_carrier_code;
                session(['airline_carrier_code' => $airlineCarrierFilterArray]);
            }
        } else {
            $airlineCarrierFilterArray = session('airline_carrier_code');
            $key = array_search($request->airline_carrier_code, $airlineCarrierFilterArray);
            if ($key !== false) {
                unset($airlineCarrierFilterArray[$key]);
            }
            session(['airline_carrier_code' => $airlineCarrierFilterArray]);
        }

    }

    public function clearAirlineCarrierFilter(Request $request){
        session()->forget('airline_carrier_code');

        Toastr::success('Filter Cleared', 'No Airline Carrier Selected');
        return back();
    }

    public function revalidateFlight($sessionIndex){
        $revlidatedData = json_decode(SabreFlightRevalidate::flightRevalidate($sessionIndex), true);

        // echo "<pre>";
        // print_r(SabreFlightRevalidate::flightRevalidate($sessionIndex));
        // echo "</pre>";
        // exit();

        // echo "<pre>";
        // print_r($revlidatedData);
        // echo "</pre>";
        // exit();

        // $jsonData = json_encode(SabreFlightRevalidate::flightRevalidate($sessionIndex), JSON_PRETTY_PRINT);
        // echo "<pre>";
        // echo $jsonData;
        // echo "</pre>";
        // exit();

        if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'])){
            return view('flight.select_flight', compact('revlidatedData'));
        } else {
            Toastr::error('Flight is not available for Booking', 'Sorry! Please Search Again');
            return redirect('/home');
        }
    }
}
