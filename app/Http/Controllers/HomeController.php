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

}
