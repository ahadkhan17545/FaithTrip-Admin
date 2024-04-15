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
        // $passengerTypes = array();
        // if($adult > 0){
        //     $passengerTypes[] = array("Code" => "ADT", "Quantity" => $adult);
        // }
        // if($child > 0){
        //     $passengerTypes[] = array("Code" => "CHD", "Quantity" => $child);
        // }
        // if($infant > 0){
        //     $passengerTypes[] = array("Code" => "INF", "Quantity" => $infant);
        // }

        // // Convert passenger types to JSON format
        // $passengerTypesJSON = json_encode($passengerTypes);

        // Sabre API request payload with dynamic query
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
        CURLOPT_POSTFIELDS =>'{
            "OTA_AirLowFareSearchRQ": {
                "Version": "2",
                "POS": {
                    "Source": [{
                            "PseudoCityCode": "S00L",
                            "RequestorID": {
                                "Type": "1",
                                "ID": "1",
                                "CompanyName": {
                                    "Code": "TN"
                                }
                            }
                        }
                    ]
                },
                "OriginDestinationInformation": [
                    {
                        "RPH": "1",
                        "DepartureDateTime": "'.$departureDate.'T00:00:00",
                        "OriginLocation": {
                            "LocationCode": "'.$originCityCode.'"
                        },
                        "DestinationLocation": {
                            "LocationCode": "'.$destinationCityCode.'"
                        }
                    }
                ],
                "TravelPreferences": {
                    "TPA_Extensions": {
                        "DataSources": {
                            "NDC": "Disable",
                            "ATPCO": "Enable",
                            "LCC": "Disable"
                        },
                        "PreferNDCSourceOnTie": {
                            "Value": true
                        }
                    }
                },
                "TravelerInfoSummary": {
                    "AirTravelerAvail": [{
                            "PassengerTypeQuantity": [
                                {
                                    "Code": "ADT",
                                    "Quantity": 1
                                }
                            ]
                        }
                    ]
                },
                "TPA_Extensions": {
                    "IntelliSellTransaction": {
                        "RequestType": {
                            "Name": "200ITINS"
                        }
                    }
                }
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Conversation-ID: ',
            'Authorization: Bearer T1RLAQJZTvYCbnUraCS7RA2R3rIXxVL+XfU733qYqOf4vSG4exCOpf/pKHaBfNFvkwU7mWwRAADg1hOncy3bfWZ977XGicQtpnnukA2FFO6+9p55HErfKqc4tWIulp12sDGDfG3hJjvF+6Kvk86/iBN+qwqzSkBh9X9aM+UL3O4vOtGt7B7scNjU9UCRFhIUSfrB9mjz/6K8JulkIioq+vwfvJWREOmFP+/z2CZy0d8IAIiNhaT6Lj41OoJPcf4YioRe94bEnL5/4sz8Ea1kZ9r5BRKMmvPDH6mt9TAhU6KXVN++xTIE97nS3oynoI8vjzNtgenY87A9cbnC+1PtGKFuGnn1Dta3sI5q+GTTtm8hvx6KCo2Iaa0*',
            'Cookie: visid_incap_2768617=CMmrEjpiT2uqtybd16i4/Ce7/2UAAAAAQUIPAAAAAAAvMTvmjB9uF7//pSsvuNc0; incap_ses_1787_2768614=CcAUVpIWFmNy74WBH7PMGF2xHGYAAAAAIpc34z3S3Q8jyR1+2Q+HMA==; nlbi_2768614=uWlMLUunkm8yyGEGRh9LCAAAAAAA3GnPshJ3E7mCKRrMlwvS; visid_incap_2768614=oagYgS2rSheFlLqzITzLq5S6/2UAAAAAQUIPAAAAAADHLck2jT6mHfxrtvT5HVcc'
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
        echo $searchResults;


    }

    public function showFlightSearchResults(){
        return view('flight.search_results');
    }
}
