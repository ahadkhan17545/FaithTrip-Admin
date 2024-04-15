<?php

namespace App\Http\Controllers;

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

    public function searchFlights(Request $request){

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
        echo $response;
    }

    public function showFlightSearchResults(){
        return view('flight.search_results');
    }
}
