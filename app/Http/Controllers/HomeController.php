<?php

namespace App\Http\Controllers;

use App\Models\SabreGdsConfig;
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

        // $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();

        // $username = base64_encode("V1:470936:S00L:AA");
        // $password = base64_encode("ft7lq3nz");
        // $authorizationHeader = base64_encode($username.":".$password);
        // // $authorizationHeader = base64_encode(base64_encode($sabreGdsInfo->production_user_id).':'.base64_encode($sabreGdsInfo->production_password));
        // echo $authorizationHeader;
        // exit();

        return view('home');
    }

    public function liveCityAirportSearch(Request $request){
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = DB::table('city_airports')->select("id", DB::raw("CONCAT(city_name, '-', airport_name) AS search_result"))
                            ->where('airport_code', 'LIKE', $search)
                            ->orWhere('city_code', 'LIKE', "%".$search."%")
                            // ->orWhere('city_name', 'LIKE', "%".$search."%")
                            // ->orWhere('airport_name', 'LIKE', "%$search%")
                            ->skip(0)
                            ->limit(5)
                            ->get();
        }

        return response()->json($data);
    }

}
