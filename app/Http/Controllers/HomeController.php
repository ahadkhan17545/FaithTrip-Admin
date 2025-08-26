<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Banner;
use App\Models\CompanyProfile;
use Yajra\DataTables\DataTables;
use App\Models\MfsAccount;
use App\Models\OfficeAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $banners = Banner::where('status', 1)->orderBy('id', 'desc')->get();
        return view('home', compact('banners'));
    }

    public function liveCityAirportSearch(Request $request){
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = DB::table('city_airports')->select("id", DB::raw("CONCAT(city_name, '-', airport_name) AS search_result"))
                            ->where(function ($query) use ($search) {
                                $query->where('airport_code', 'LIKE', $search."%")
                                    ->orWhere('city_code', 'LIKE', $search."%")
                                    ->orWhere('city_name', 'LIKE', $search."%");
                            })
                            ->skip(0)
                            ->limit(5)
                            ->get();
        }

        return response()->json($data);
    }

    public function liveAirlineSearch(Request $request){
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = DB::table('airlines')
                        ->select("id", "iata as search_result")
                        ->whereNotNull('iata')
                        ->where('active', 'Y')
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%".$search."%")
                                ->orWhere('iata', 'LIKE', "%".$search."%");
                        })
                        ->skip(0)
                        ->limit(5)
                        ->get();
        }

        return response()->json($data);
    }

    public function passangerLiveSearch(Request $request){
        $searchPassangers = DB::table('saved_passangers')
                        ->where('contact', 'LIKE', '%'.$request->search_keyword.'%')
                        ->where('saved_by', Auth::user()->id)
                        ->orderBy('first_name', 'asc')
                        ->skip(0)
                        ->limit(5)
                        ->get();

        $searchResults = view('flight.live_search_passangers', compact('searchPassangers'))->render();
        return response()->json(['searchResults' => $searchResults]);
    }

    public function paymentMethods()
    {
        $bankAccounts = BankAccount::where('status', 1)->orderBy('id', 'asc')->get();
        $mfsAccounts = MfsAccount::where('status', 1)->orderBy('id', 'asc')->get();
        $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
        $officeAddress = OfficeAddress::where('status', 1)->orderBy('id', 'desc')->get();
        return view('payment_method', compact('bankAccounts', 'mfsAccounts', 'companyProfile', 'officeAddress'));
    }

    public function viewActivityLogs(Request $request){
        if ($request->ajax()) {

            $query = DB::table('activity_logs')
                        ->leftJoin('users', 'activity_logs.user_id', 'users.id')
                        ->select('activity_logs.*', 'users.name as user_name')
                        ->orderBy('id', 'desc');

            return Datatables::of($query)
                    ->filterColumn('user_name', function($query, $keyword) {
                        $query->where('users.name', 'like', "%{$keyword}%");
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('activity_logs');
    }

}
