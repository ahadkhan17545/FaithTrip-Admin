<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DateTime;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class FlightSearchController extends Controller
{
    public function generateAccessToken(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.cert.platform.sabre.com/v2/auth/token',
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
                'Authorization: Basic VmpFNmFIaHdObU41TVRRMVltcDJOV2g1T1RwRVJWWkRSVTVVUlZJNlJWaFU6U0hBNGRGUTJhVTQ',
                'Cookie: visid_incap_2768617=CMmrEjpiT2uqtybd16i4/Ce7/2UAAAAAQUIPAAAAAAAvMTvmjB9uF7//pSsvuNc0; incap_ses_1787_2768614=CcAUVpIWFmNy74WBH7PMGF2xHGYAAAAAIpc34z3S3Q8jyR1+2Q+HMA==; nlbi_2768614=uWlMLUunkm8yyGEGRh9LCAAAAAAA3GnPshJ3E7mCKRrMlwvS; visid_incap_2768614=oagYgS2rSheFlLqzITzLq5S6/2UAAAAAQUIPAAAAAADHLck2jT6mHfxrtvT5HVcc'
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

    public function getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType){


        // travellers info
        $passengerTypes = array();
        if ($adult > 0) {
            $passengerTypes[] = array("Code" => "ADT", "Quantity" => (int) $adult);
        }
        if ($child > 0) {
            $passengerTypes[] = array("Code" => "CHD", "Quantity" => (int) $child);
        }
        if ($infant > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (int) $infant);
        }

        $airTravelerAvail = [];
        foreach ($passengerTypes as $passengerType) {
            $airTravelerAvail[] = array(
                "PassengerTypeQuantity" => array($passengerType)
            );
        }

        // oneway or return flights
        $flightTypeData = array();
        if($flightType == 1){
            $flightTypeData[] = array("RPH" => "1", "DepartureDateTime" => "$departureDate" . "T00:00:00", "OriginLocation" => array("LocationCode" => $originCityCode),"DestinationLocation" => array("LocationCode" => $destinationCityCode));
        } else {
            $flightTypeData[] = array("RPH" => "1", "DepartureDateTime" => "$departureDate" . "T00:00:00", "OriginLocation" => array("LocationCode" => $originCityCode),"DestinationLocation" => array("LocationCode" => $destinationCityCode));
            $flightTypeData[] = array("RPH" => "1", "DepartureDateTime" => "$returnDate" . "T00:00:00", "OriginLocation" => array("LocationCode" => $destinationCityCode),"DestinationLocation" => array("LocationCode" => $originCityCode));
        }

        // Sabre API request payload with dynamic query
        $accessToken = session('access_token');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cert.platform.sabre.com/v5/offers/shop',
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
                                "Name" => "200ITINS"
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
        $originCityCode = DB::table('city_airports')->where('id', $departureLocationId)->first()->city_code;
        $destinationLocationId = $request->destination_location_id;
        $destinationCityCode = DB::table('city_airports')->where('id', $destinationLocationId)->first()->city_code;
        $departureDate = date("Y-m-d", strtotime($request->departure_date));
        $returnDate = date("Y-m-d", strtotime($request->return_date));
        $adult = $request->adult;
        $child = $request->child;
        $infant = $request->infant;
        $flightType = $request->flight_type;

        // storing search query into session for modify search
        session([
            'departure_location_id' => $departureLocationId,
            'origin_city_Code' => $originCityCode,
            'destination_location_id' => $destinationLocationId,
            'destination_City_Code' => $destinationCityCode,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'adult' => $adult,
            'child' => $child,
            'infant' => $infant,
            'flight_type' => $flightType,
        ]);

        $searchResults = $this->getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType);
        session(['search_results' => $searchResults]);


        // for carrier filters
        $searchResults = json_decode($searchResults, true);
        $operatingCodes = [];
        foreach ($searchResults['groupedItineraryResponse']['scheduleDescs'] as $schedule) {
            $operatingCodes[] = $schedule['carrier']['operating'];
        }
        $operatingCodes = array_unique($operatingCodes);
        session(['search_results_operating_carriers' => $operatingCodes]);
        session()->forget('filter_min_price');
        session()->forget('filter_max_price');

    }

    public function showFlightSearchResults(){
        $searchResults = json_decode(session('search_results'), true);
        $search_results_operating_carriers = session('search_results_operating_carriers');
        return view('flight.search_results', compact('searchResults', 'search_results_operating_carriers'));
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
}
