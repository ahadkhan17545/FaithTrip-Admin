<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('home');
    }

    public function liveCityAirportSearch(Request $request){
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = DB::table('city_airports')->select("id", DB::raw("CONCAT(city_name, '-', airport_name) AS search_result"))
                            ->where('city_name', 'LIKE', "%$search%")
                            ->orWhere('airport_name', 'LIKE', "%$search%")
                            ->orWhere('airport_code', 'LIKE', "%$search%")
                            ->orWhere('city_code', 'LIKE', "%$search%")
                            ->skip(0)
                            ->limit(5)
                            ->get();
        }

        return response()->json($data);
    }

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

    public function getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $adult, $child, $infant){

        // Define passenger types and quantities
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

        // Transform passenger types into the required format
        $airTravelerAvail = [];
        foreach ($passengerTypes as $passengerType) {
            $airTravelerAvail[] = array(
                "PassengerTypeQuantity" => array($passengerType)
            );
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
                    "Version" => "2",
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
                    "OriginDestinationInformation" => array(
                        array(
                            "RPH" => "1",
                            "DepartureDateTime" => "$departureDate" . "T00:00:00",
                            "OriginLocation" => array(
                                "LocationCode" => $originCityCode
                            ),
                            "DestinationLocation" => array(
                                "LocationCode" => $destinationCityCode
                            )
                        )
                    ),
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
        // $returnDate = $request->return_date;
        $adult = $request->adult;
        $child = $request->child;
        $infant = $request->infant;

        $searchResults = $this->getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $adult, $child, $infant);
        session(['search_results' => $searchResults]);

    }

    public function showFlightSearchResults(){
        $searchResults = json_decode(session('search_results'), true);
        return view('flight.search_results', compact('searchResults'));
    }
}
