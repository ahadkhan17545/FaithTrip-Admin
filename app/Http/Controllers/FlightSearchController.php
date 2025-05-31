<?php

namespace App\Http\Controllers;

use App\Models\FlyhubFlightSearch;
use App\Models\Gds;
use App\Models\SabreFlightRevalidate;
use App\Models\FlyhubFlightRevalidate;
use App\Models\SabreFlightSearch;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlightSearchController extends Controller
{
    public function searchFlights(Request $request){

        $departureLocationId = $request->departure_location_id;
        $originCityInfo = DB::table('city_airports')->where('id', $departureLocationId)->first();
        $originCityCode = $originCityInfo->airport_code;
        $destinationLocationId = $request->destination_location_id;
        $destinationCityInfo = DB::table('city_airports')->where('id', $destinationLocationId)->first();
        $destinationCityCode = $destinationCityInfo->airport_code;
        $departureDate = date("Y-m-d", strtotime($request->departure_date));
        $returnDate = $request->return_date ? date("Y-m-d", strtotime($request->return_date)) : null;
        $adult = $request->adult;
        $child = $request->child;
        $infant = $request->infant;
        $flightType = $request->flight_type;
        $cabinClass = $request->cabin_class;


        // preferred airlines code start
        $preferredAirlines = $request->preferred_airlines; //comma separated id of airnline like 218,1359
        $airlinePrefs = null;

        $preferredAirlinesArray = [];
        if($preferredAirlines){
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
            'preferred_airlines' => $preferredAirlinesArray,
            'cabin_class' => $cabinClass,
        ]);

        // sabre
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){
            $searchResults = SabreFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
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

            // session time out for search results
            $currentDateTime = date('Y-m-d H:i:s');
            $sessionTimeOutAt = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($currentDateTime)));
            session(['session_timeout_at' => $sessionTimeOutAt]);
        }

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $searchResults = FlyhubFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $cabinClass, $preferredAirlinesArray);
            session(['search_results' => $searchResults]);

            // for carrier filters
            $operatingCodes = [];
            if(count($searchResults)){
                foreach ($searchResults as $searchResult) {
                    $operatingCodes[] = $searchResult['operating_carrier_code'];
                }
            }
            $operatingCodes = array_values(array_unique($operatingCodes));
            session(['search_results_operating_carriers' => $operatingCodes]);
        }

        session()->forget('filter_min_price');
        session()->forget('filter_max_price');
        session()->forget('airline_carrier_code');

        return response()->json(['success' => 'Search Completed Successfully']);
    }

    public function showFlightSearchResults(){

        if(Auth::user()->search_status == 0){
            Toastr::error('Flight Search Permission Denied');
            return back();
        }

        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){
            $searchResults = json_decode(session('search_results'), true);
            $search_results_operating_carriers = session('search_results_operating_carriers');
            return view('flight.search_results', compact('searchResults', 'search_results_operating_carriers'));
        }

        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $searchResults = session('search_results');
            $search_results_operating_carriers = session('search_results_operating_carriers');
            return view('common.flight.searchResults', compact('searchResults', 'search_results_operating_carriers'));
        }

    }

    public function searchNextDay(){

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
        $preferredAirlinesArray = session('preferred_airlines');
        $cabinClass = session('cabin_class');

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
            'preferred_airlines' => $preferredAirlinesArray,
            'cabin_class' => $cabinClass,
        ]);


        // sabre
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){

            $airlinePrefs = null;
            if(count($preferredAirlinesArray) > 0){
                $airlinePrefs = array_map(function($code) {
                    return ["Code" => $code];
                }, $preferredAirlinesArray);
            }

            $searchResults = SabreFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
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
        }

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $searchResults = FlyhubFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $cabinClass, $preferredAirlinesArray);
            session(['search_results' => $searchResults]);

            // for carrier filters
            $operatingCodes = [];
            if(count($searchResults)){
                foreach ($searchResults as $searchResult) {
                    $operatingCodes[] = $searchResult['operating_carrier_code'];
                }
            }
            $operatingCodes = array_values(array_unique($operatingCodes));
            session(['search_results_operating_carriers' => $operatingCodes]);
        }

        session()->forget('filter_min_price');
        session()->forget('filter_max_price');
        session()->forget('airline_carrier_code');

        return redirect('flight/search-results');
    }

    public function searchPreviousDay(){

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
        $preferredAirlinesArray = session('preferred_airlines');
        $cabinClass = session('cabin_class');

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
            'preferred_airlines' => $preferredAirlinesArray,
            'cabin_class' => $cabinClass,
        ]);

        // sabre
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){

            $airlinePrefs = null;
            if(count($preferredAirlinesArray) > 0){
                $airlinePrefs = array_map(function($code) {
                    return ["Code" => $code];
                }, $preferredAirlinesArray);
            }

            $searchResults = SabreFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs);
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

        }

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $searchResults = FlyhubFlightSearch::getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $cabinClass, $preferredAirlinesArray);
            session(['search_results' => $searchResults]);

            // for carrier filters
            $operatingCodes = [];
            if(count($searchResults)){
                foreach ($searchResults as $searchResult) {
                    $operatingCodes[] = $searchResult['operating_carrier_code'];
                }
            }
            $operatingCodes = array_values(array_unique($operatingCodes));
            session(['search_results_operating_carriers' => $operatingCodes]);
        }

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

        if(Auth::user()->booking_status == 0){
            Toastr::error('Flight Booking Permission Denied');
            return back();
        }

        // sabre
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){
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

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $revalidatedData = FlyhubFlightRevalidate::flightRevalidate($sessionIndex);

            // echo "<pre>";
            // print_r($revalidatedData);
            // echo "</pre>";

            if(isset($revalidatedData['departure_datetime'])){
                return view('common.flight.selectFlight', compact('revalidatedData'));
            } else {
                Toastr::error('Flight is not available for Booking', 'Sorry! Please Search Again');
                return redirect('/home');
            }

        }

    }
}
