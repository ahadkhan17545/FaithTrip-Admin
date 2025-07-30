<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\FlightBooking;
use App\Models\FlyhubFlightBooking;
use App\Models\FlyhubFlightTicketIssue;
use App\Models\FlyhubGdsConfig;
use App\Models\Gds;
use App\Models\SavedPassanger;
use App\Models\User;
use Yajra\DataTables\DataTables;
use App\Models\FlightPassanger;
use App\Models\FlightSegment;
use App\Models\SabreBookingDetails;
use App\Models\SabreFlightBooking;
use App\Models\SabreFlightTicketIssue;
use App\Models\SabreGdsConfig;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FlightBookingController extends Controller
{
    public function bookFlightWithPnrSabre(Request $request){

        $revlidatedData = session('revlidatedData');

        if(isset($request->first_name[0]) && $request->first_name[0] && isset($request->last_name[0]) && $request->last_name[0] && $request->traveller_contact && $request->traveller_email && isset($request->titles[0]) && $request->titles[0] && isset($request->dob[0]) && $request->dob[0]){

            $onlineBookingInfo = json_decode(SabreFlightBooking::flightBooking($revlidatedData, $request->traveller_contact, $request->traveller_email,  $request->first_name, $request->last_name, $request->titles, $request->dob, $request->passanger_type, $request->age, $request->document_issue_country, $request->nationality, $request->document_no, $request->document_expire_date), true);

        } else {
            Toastr::error('Passanger Information Missing', 'Failed');
            return redirect('/home');
        }

        // echo "<pre>";
        // echo SabreFlightBooking::flightBooking($revlidatedData, $request->traveller_contact, $request->traveller_email,  $request->first_name, $request->last_name, $request->titles, $request->dob, $request->passanger_type, $request->age, $request->document_issue_country, $request->nationality, $request->document_no, $request->document_expire_date);
        // echo "</pre>";
        // exit();

        $bookinPnrID = null;
        $bookingResponse = json_encode($onlineBookingInfo, true);
        if(isset($onlineBookingInfo['CreatePassengerNameRecordRS']['ApplicationResults']['status']) && $onlineBookingInfo['CreatePassengerNameRecordRS']['ApplicationResults']['status'] == 'Complete'){
            $bookinPnrID = $onlineBookingInfo['CreatePassengerNameRecordRS']['ItineraryRef']['ID'];
            $status = 1;
        } else{
            $status = 0;
        }

        // fetching price using session for security (not from hidden field)
        $revlidatedData = session('revlidatedData');
        if($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareCurrency'] == 'USD'){
            $base_fare_amount = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareAmount'] * $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['currencyConversion']['exchangeRateUsed'];
        }
        else{
            $base_fare_amount = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareAmount'];
        }
        $total_tax_amount = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalTaxAmount'];
        $total_fare = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalPrice'];

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();

        $departureAirportCode = DB::table('city_airports')->where('id', session('departure_location_id'))->first()->airport_code;
        $arrivalAirportCode = DB::table('city_airports')->where('id', session('destination_location_id'))->first()->airport_code;

        $flightBookingId = FlightBooking::insertGetId([
            'flight_type' => session('flight_type'), // 1 = one way, 2 = round trip
            'booking_no' => str::random(3) . "-" . time(),
            "source" => 1, //portal
            'booked_by' => Auth::user()->id,
            'b2b_comission' => Auth::user()->comission,
            'pnr_id' => $bookinPnrID,
            'booking_id' => null,
            'gds' => $request->gds,
            'gds_unique_id' => $request->gds_unique_id,
            'traveller_name' => $request->traveller_name,
            'traveller_email' => $request->traveller_email,
            'traveller_contact' => $request->traveller_contact,
            'departure_date' => $request->departure_date,
            'departure_location' => $departureAirportCode, //$request->departure_location,
            'arrival_location' => $arrivalAirportCode, //$request->arrival_location,
            'governing_carriers' => $request->governing_carriers,
            'adult' => session('adult'),
            'child' => session('child'),
            'infant' => session('infant'),
            'base_fare_amount' => $base_fare_amount,
            'total_tax_amount' => $total_tax_amount,
            'total_fare' => $total_fare,
            'currency' => $request->currency,
            'last_ticket_datetime' => null, //will extract from getbooking response later
            'booking_request' => session('booking_request'),
            'booking_response' => $bookingResponse,
            'get_booking_response' => null,
            'status' => $status,
            'payment_status' => null,
            'is_live' => $sabreGdsInfo ? $sabreGdsInfo->is_production : 0,
            'created_at' => Carbon::now()
        ]);


        $segmentArray = [];
        $legsArray = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['legs'];
        foreach ($legsArray as $leg) {
            $legRef = $leg['ref'] - 1;
            $legDescription = $revlidatedData['groupedItineraryResponse']['legDescs'][$legRef];
            $schedulesArray = $legDescription['schedules'];
            foreach ($schedulesArray as $schedule) {
                $scheduleRef = $schedule['ref'] - 1;
                $segmentArray[] = $revlidatedData['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
            }
        }

        foreach ($segmentArray as $segmentData){

            FlightSegment::insert([
                'flight_booking_id' => $flightBookingId,
                'total_miles_flown' => $segmentData['totalMilesFlown'],
                'elapsed_time' => $segmentData['elapsedTime'],
                'booking_code' => null, //update while visiting booking details from getbooking response
                'cabin_code' => null, //update while visiting booking details from getbooking response
                'baggage_allowance' => null, //update while visiting booking details from getbooking response
                'departure_airport_code' => $segmentData['departure']['airport'],
                'departure_city_code' => $segmentData['departure']['city'],
                'departure_country_code' => $segmentData['departure']['country'],
                'departure_time' => $segmentData['departure']['time'],
                'departure_terminal' => isset($segmentData['departure']['terminal']) ? $segmentData['departure']['terminal'] : null,
                'arrival_airport_code' => $segmentData['arrival']['airport'],
                'arrival_city_code' => $segmentData['arrival']['city'],
                'arrival_country_code' => $segmentData['arrival']['country'],
                'arrival_time' => $segmentData['arrival']['time'],
                'arrival_terminal' => isset($segmentData['arrival']['terminal']) ? $segmentData['arrival']['terminal'] : null,
                'carrier_marketing_code' => $segmentData['carrier']['marketing'],
                'carrier_marketing_flight_number' => $segmentData['carrier']['marketingFlightNumber'],
                'carrier_operating_code' => $segmentData['carrier']['operating'],
                'carrier_operating_flight_number' => $segmentData['carrier']['operatingFlightNumber'],
                'carrier_equipment_code' => $segmentData['carrier']['equipment']['code'],
                'created_at' => Carbon::now()
            ]);

        }

        foreach($request->first_name as $passangerIndex => $firstName){

            if(is_array($request->save_passanger) && count($request->save_passanger) > 0 && in_array($passangerIndex, $request->save_passanger)){

                $savedPassanger = SavedPassanger::where([
                                        ['first_name', $firstName],
                                        ['last_name', $request->last_name[$passangerIndex]],
                                        ['dob', '=', $request->dob[$passangerIndex]]
                                    ])->first();

                if(!$savedPassanger){
                    $savedPassanger = new SavedPassanger();
                }

                $savedPassanger->saved_by = Auth::user()->id;
                $savedPassanger->email = $request->email[$passangerIndex];
                $savedPassanger->contact = $request->phone[$passangerIndex];
                $savedPassanger->type = $request->passanger_type[$passangerIndex];
                $savedPassanger->title = $request->titles[$passangerIndex];
                $savedPassanger->first_name = $firstName;
                $savedPassanger->last_name = $request->last_name[$passangerIndex];
                $savedPassanger->dob = $request->dob[$passangerIndex];
                $savedPassanger->age = str_pad($request->age[$passangerIndex],2,"0",STR_PAD_LEFT);
                $savedPassanger->document_type = $request->document_type[$passangerIndex];
                $savedPassanger->document_no = $request->document_no[$passangerIndex];
                $savedPassanger->document_expire_date = $request->document_expire_date[$passangerIndex];
                $savedPassanger->document_issue_country = $request->document_issue_country[$passangerIndex];
                $savedPassanger->nationality = $request->nationality[$passangerIndex];
                $savedPassanger->frequent_flyer_no = $request->frequent_flyer_no[$passangerIndex];
                $savedPassanger->created_at = Carbon::now();
                $savedPassanger->save();
            }

            FlightPassanger::insert([
                'flight_booking_id' => $flightBookingId,
                'passanger_type' => $request->passanger_type[$passangerIndex],
                'title' => $request->titles[$passangerIndex],
                'first_name' => $firstName,
                'last_name' => $request->last_name[$passangerIndex],
                'email' => $request->email[$passangerIndex],
                'phone' => $request->phone[$passangerIndex],
                'dob' => $request->dob[$passangerIndex],
                'age' => str_pad($request->age[$passangerIndex],2,"0",STR_PAD_LEFT),
                'document_type' => $request->document_type[$passangerIndex],
                'document_no' => $request->document_no[$passangerIndex],
                'document_expire_date' => $request->document_expire_date[$passangerIndex],
                'document_issue_country' => $request->document_issue_country[$passangerIndex],
                'nationality' => $request->nationality[$passangerIndex],
                'frequent_flyer_no' => $request->frequent_flyer_no[$passangerIndex],
                'created_at' => Carbon::now()
            ]);
        }


        session()->forget(['adult', 'child', 'infant', 'revlidatedData', 'booking_request']);

        if($status == 0){
            Toastr::success('Flight Booking Request Sent', 'Success');
            return redirect('/view/all/booking');
        } else {
            Toastr::success('Flight Booked Successfully', 'Success');
            return redirect('/view/all/booking');
        }

    }

    public function bookFlightWithPnr(Request $request){
        $revalidatedData = session('revalidatedData');

        // print_r($revalidatedData['segments']);
        // exit();

        // 1st step updateing passangers before Booking
        $travellerUpdateResponse = json_decode(FlyhubFlightBooking::updateTravellers($request, $revalidatedData), true);

        if($travellerUpdateResponse['status'] != 'success'){
            Toastr::error('Failed to update traveller response');
            return back();
        }

        // print_r($travellerUpdateResponse);
        // exit();

        // 2nd step booking
        $bookingResponse = json_decode(FlyhubFlightBooking::createBooking($request, $revalidatedData), true);

        // print_r($bookingResponse);
        // exit();

        if($bookingResponse['status'] != 'success'){
            Toastr::error('Failed to Book this Flight');
            return back();
        }

        $bookingID = $bookingResponse['general']['booking_id'];
        $airlines_pnr = $bookingResponse['general']['airlines_pnr'];
        $rawBookingResponse = json_encode($bookingResponse, true);
        $status = 1;
        $flyhubGdsInfo = FlyhubGdsConfig::where('id', 1)->first();

        $flightBookingId = FlightBooking::insertGetId([
            'flight_type' => session('flight_type'), // 1 = one way, 2 = round trip
            'booking_no' => str::random(3) . "-" . time(),
            "source" => 1, //portal
            'booked_by' => Auth::user()->id,
            'b2b_comission' => Auth::user()->comission,
            'pnr_id' => null,
            'booking_id' => $bookingID,
            'airlines_pnr' => $airlines_pnr,
            'gds' => $request->gds,
            'gds_unique_id' => $request->gds_unique_id,
            'traveller_name' => $request->traveller_name,
            'traveller_email' => $request->traveller_email,
            'traveller_contact' => $request->traveller_contact,
            'departure_date' => date("Y-m-d h:i:s", strtotime($request->departure_date)),
            'departure_location' => DB::table('city_airports')->where('id', session('departure_location_id'))->first()->airport_code, //$request->departure_location,
            'arrival_location' => DB::table('city_airports')->where('id', session('destination_location_id'))->first()->airport_code, //$request->arrival_location,
            'governing_carriers' => $request->governing_carriers,
            'adult' => session('adult'),
            'child' => session('child'),
            'infant' => session('infant'),
            'base_fare_amount' => $revalidatedData['base_fare_amount'],
            'total_tax_amount' => $revalidatedData['total_tax_amount'],
            'total_fare' => $revalidatedData['total_fare'],
            'currency' => $request->currency,
            'last_ticket_datetime' => $request->last_ticket_datetime ? date("Y-m-d h:i:s", strtotime($request->last_ticket_datetime)) : null,
            'booking_response' => $rawBookingResponse,
            'status' => $status,
            'payment_status' => null,
            'is_live' => $flyhubGdsInfo ? $flyhubGdsInfo->is_production : 0,
            'created_at' => Carbon::now()
        ]);

        $onwardSegmentArray[] = $revalidatedData['segments'];
        $returnSegmentArray[] = isset($revalidatedData['return_segments']) ? $revalidatedData['return_segments'] : array();

        if(count($onwardSegmentArray) > 0){
            foreach ($onwardSegmentArray as $onwardSegmentIndex => $segmentData){

                $departureZone = DB::table('city_airports')->where('city_name', $segmentData[$onwardSegmentIndex]['departure_city_name'])->first();
                $arrivalZone = DB::table('city_airports')->where('city_name', $segmentData[$onwardSegmentIndex]['arrival_city_name'])->first();

                FlightSegment::insert([

                    'flight_booking_id' => $flightBookingId,
                    'total_miles_flown' => $segmentData[$onwardSegmentIndex]['miles_flown'],
                    'elapsed_time' => $segmentData[$onwardSegmentIndex]['elapsed_time'],
                    'booking_code' => $segmentData[$onwardSegmentIndex]['booking_code'],
                    'cabin_code' => $segmentData[$onwardSegmentIndex]['cabin_code'],
                    // 'baggage_allowance' => $segmentData[$onwardSegmentIndex]['baggage_allowance'], //its an array

                    'departure_airport_code' => $segmentData[$onwardSegmentIndex]['departure_airport_code'],
                    'departure_city_code' => $departureZone ? $departureZone->city_code : null,
                    'departure_country_code' => $departureZone ? $departureZone->country_code : null,
                    'departure_time' => date("Y-m-d h:i:s", strtotime($segmentData[$onwardSegmentIndex]['departure_datetime'])),
                    'departure_terminal' => $segmentData[$onwardSegmentIndex]['departure_terminal'],

                    'arrival_airport_code' => $segmentData[$onwardSegmentIndex]['arrival_airport_code'],
                    'arrival_city_code' => $arrivalZone ? $arrivalZone->city_code : null,
                    'arrival_country_code' => $arrivalZone ? $arrivalZone->country_code : null,
                    'arrival_time' => date("Y-m-d h:i:s", strtotime($segmentData[$onwardSegmentIndex]['arrival_datetime'])),
                    'arrival_terminal' => $segmentData[$onwardSegmentIndex]['arrival_terminal'],

                    'carrier_marketing_code' => $segmentData[$onwardSegmentIndex]['marketing_carrier_code'],
                    'carrier_marketing_flight_number' => $segmentData[$onwardSegmentIndex]['marketing_flight_number'],
                    'carrier_operating_code' => $segmentData[$onwardSegmentIndex]['operating_carrier_code'],
                    'carrier_operating_flight_number' => $segmentData[$onwardSegmentIndex]['operating_flight_number'],
                    'carrier_equipment_code' => null,
                    'created_at' => Carbon::now()
                ]);
            }
        }

        if(count($returnSegmentArray) > 0 && isset($revalidatedData['return_segments'])){
            foreach ($returnSegmentArray as $returnSegmentIndex => $segmentData){

                $departureZone = DB::table('city_airports')->where('city_name', $segmentData[$returnSegmentIndex]['departure_city_name'])->first();
                $arrivalZone = DB::table('city_airports')->where('city_name', $segmentData[$returnSegmentIndex]['arrival_city_name'])->first();

                FlightSegment::insert([

                    'flight_booking_id' => $flightBookingId,
                    'total_miles_flown' => $segmentData[$returnSegmentIndex]['miles_flown'],
                    'elapsed_time' => $segmentData[$returnSegmentIndex]['elapsed_time'],
                    'booking_code' => $segmentData[$returnSegmentIndex]['booking_code'],
                    'cabin_code' => $segmentData[$returnSegmentIndex]['cabin_code'],
                    // 'baggage_allowance' => $segmentData[$returnSegmentIndex]['baggage_allowance'],

                    'departure_airport_code' => $segmentData[$returnSegmentIndex]['departure_airport_code'],
                    'departure_city_code' => $departureZone ? $departureZone->city_code : null,
                    'departure_country_code' => $departureZone ? $departureZone->country_code : null,
                    'departure_time' => date("Y-m-d h:i:s", strtotime($segmentData[$returnSegmentIndex]['departure_datetime'])),
                    'departure_terminal' => $segmentData[$returnSegmentIndex]['departure_terminal'],

                    'arrival_airport_code' => $segmentData[$returnSegmentIndex]['arrival_airport_code'],
                    'arrival_city_code' => $arrivalZone ? $arrivalZone->city_code : null,
                    'arrival_country_code' => $arrivalZone ? $arrivalZone->country_code : null,
                    'arrival_time' => date("Y-m-d h:i:s", strtotime($segmentData[$returnSegmentIndex]['arrival_datetime'])),
                    'arrival_terminal' => $segmentData[$returnSegmentIndex]['arrival_terminal'],

                    'carrier_marketing_code' => $segmentData[$returnSegmentIndex]['marketing_carrier_code'],
                    'carrier_marketing_flight_number' => $segmentData[$returnSegmentIndex]['marketing_flight_number'],
                    'carrier_operating_code' => $segmentData[$returnSegmentIndex]['operating_carrier_code'],
                    'carrier_operating_flight_number' => $segmentData[$returnSegmentIndex]['operating_flight_number'],
                    'carrier_equipment_code' => null,
                    'created_at' => Carbon::now()
                ]);
            }
        }

        foreach($request->first_name as $passangerIndex => $firstName){

            if(is_array($request->save_passanger) && count($request->save_passanger) > 0 && in_array($passangerIndex, $request->save_passanger)){

                $savedPassanger = SavedPassanger::where([
                                        ['first_name', $firstName],
                                        ['last_name', $request->last_name[$passangerIndex]],
                                        ['dob', '=', $request->dob[$passangerIndex]]
                                    ])->first();

                if(!$savedPassanger){
                    $savedPassanger = new SavedPassanger();
                }

                $savedPassanger->saved_by = Auth::user()->id;
                $savedPassanger->email = $request->email[$passangerIndex];
                $savedPassanger->contact = $request->phone[$passangerIndex];
                $savedPassanger->type = $request->passanger_type[$passangerIndex];
                $savedPassanger->title = $request->titles[$passangerIndex];
                $savedPassanger->first_name = $firstName;
                $savedPassanger->last_name = $request->last_name[$passangerIndex];
                $savedPassanger->dob = $request->dob[$passangerIndex];
                $savedPassanger->age = str_pad($request->age[$passangerIndex],2,"0",STR_PAD_LEFT);
                $savedPassanger->document_type = $request->document_type[$passangerIndex];
                $savedPassanger->document_no = $request->document_no[$passangerIndex];
                $savedPassanger->document_expire_date = $request->document_expire_date[$passangerIndex];
                $savedPassanger->document_issue_country = $request->document_issue_country[$passangerIndex];
                $savedPassanger->nationality = $request->nationality[$passangerIndex];
                $savedPassanger->frequent_flyer_no = $request->frequent_flyer_no[$passangerIndex];
                $savedPassanger->created_at = Carbon::now();
                $savedPassanger->save();
            }

            FlightPassanger::insert([
                'flight_booking_id' => $flightBookingId,
                'passanger_type' => $request->passanger_type[$passangerIndex],
                'title' => $request->titles[$passangerIndex],
                'first_name' => $firstName,
                'last_name' => $request->last_name[$passangerIndex],
                'email' => $request->email[$passangerIndex],
                'phone' => $request->phone[$passangerIndex],
                'dob' => $request->dob[$passangerIndex],
                'age' => str_pad($request->age[$passangerIndex],2,"0",STR_PAD_LEFT),
                'document_type' => $request->document_type[$passangerIndex],
                'document_no' => $request->document_no[$passangerIndex],
                'document_expire_date' => $request->document_expire_date[$passangerIndex],
                'document_issue_country' => $request->document_issue_country[$passangerIndex],
                'nationality' => $request->nationality[$passangerIndex],
                'frequent_flyer_no' => $request->frequent_flyer_no[$passangerIndex],
                'created_at' => Carbon::now()
            ]);
        }

        session()->forget(['adult', 'child', 'infant', 'revlidatedData']);
        Toastr::success('Flight Booking Request Sent', 'Success');
        return redirect('/view/all/booking');

    }

    public function viewAllBooking(Request $request){

        if ($request->ajax()) {

            // removing log coloumns
            $columns = Schema::getColumnListing('flight_bookings');
            $excluded = ['booking_request', 'booking_response', 'get_booking_response', 'ticketing_response'];
            $columns = array_diff($columns, $excluded);
            $columns = array_map(function ($col) {
                return "flight_bookings.$col";
            }, $columns);

            if(Auth::user()->user_type == 1){

                $query = DB::table('flight_bookings')
                        ->leftJoin('users', 'flight_bookings.booked_by', '=', 'users.id')
                        ->select([...$columns, 'users.name as b2b_user'])
                        ->where(function ($q) {
                            $q->where('flight_bookings.status', 1)
                            ->orWhere('flight_bookings.status', 0);
                        })
                        ->orderBy('flight_bookings.id', 'desc');

            } else {

                $query = FlightBooking::where('booked_by', Auth::user()->id)
                                        ->where(function ($q) {
                                            $q->where('status', 1)
                                            ->orWhere('status', 0);
                                        })
                                        ->select([...$columns])
                                        ->orderBy('id', 'desc');
            }

            return Datatables::of($query)
                    ->addColumn('flight_routes', function($data){
                        $routeString = $data->departure_location." - ".$data->arrival_location;
                        if($data->flight_type == 2){
                            $routeString .= " - ".$data->departure_location;
                        }
                        return $routeString;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->editColumn('total_fare', function($data) {
                        return $data->currency." ".number_format($data->total_fare);
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:goldenrod'>Booking Request</span>";
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Booked</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:green'>Issued</span>";
                        if($data->status == 3)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";
                        if($data->status == 4)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";

                    })
                    ->addColumn('total_passangers', function($data){
                        return $data->adult+$data->child+$data->infant;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('flight/booking/details')."/".$data->booking_no.'" class="btn-sm btn-info text-white rounded d-inline-block mb-1"><i class="fas fa-eye"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('booking.view');

    }

    public function viewCancelBooking(Request $request){

        if ($request->ajax()) {

            // removing log coloumns
            $columns = Schema::getColumnListing('flight_bookings');
            $excluded = ['booking_request', 'booking_response', 'get_booking_response', 'ticketing_response'];
            $columns = array_diff($columns, $excluded);
            $columns = array_map(function ($col) {
                return "flight_bookings.$col";
            }, $columns);

            if(Auth::user()->user_type == 1){

                $query = DB::table('flight_bookings')
                        ->leftJoin('users', 'flight_bookings.booked_by', '=', 'users.id')
                        ->select([...$columns, 'users.name as b2b_user'])
                        ->where('flight_bookings.status', 3)
                        ->orderBy('flight_bookings.id', 'desc');

            } else {

                $query = FlightBooking::where('booked_by', Auth::user()->id)
                                        ->where('status', 3)
                                        ->select([...$columns])
                                        ->orderBy('id', 'desc');

            }

            return Datatables::of($query)
                    ->addColumn('flight_routes', function($data){
                        $routeString = $data->departure_location." - ".$data->arrival_location;
                        if($data->flight_type == 2){
                            $routeString .= " - ".$data->departure_location;
                        }
                        return $routeString;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->editColumn('total_fare', function($data) {
                        return $data->currency." ".number_format($data->total_fare);
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Booked</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:green'>Issued</span>";
                        if($data->status == 3)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";
                        if($data->status == 4)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";

                    })
                    ->addColumn('total_passangers', function($data){
                        return $data->adult+$data->child+$data->infant;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('flight/booking/details')."/".$data->booking_no.'" class="btn-sm btn-info text-white rounded d-inline-block mb-1"><i class="fas fa-eye"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Cancel" class="btn-sm btn-danger rounded d-inline-block cancelBtn"><i class="fas fa-times-circle"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('booking.cancelled');

    }

    public function flightBookingDetails($bookingNo){

        $flightBookingDetails = FlightBooking::where('booking_no', $bookingNo)->first();

        if($flightBookingDetails->gds == "Sabre" && ($flightBookingDetails->status == 1 || $flightBookingDetails->status == 2)){
            SabreBookingDetails::getBookingDetails($flightBookingDetails->pnr_id);
            $flightBookingDetails = FlightBooking::where('booking_no', $bookingNo)->first();
        }

        $bookingResSegs = null;
        if($flightBookingDetails->booking_response){
            $bookingRes = json_decode($flightBookingDetails->booking_response, true);
            $bookingResSegs = $bookingRes['CreatePassengerNameRecordRS']['TravelItineraryRead']['TravelItinerary']['ItineraryInfo']['ReservationItems']['Item'];
        }

        $flightSegments = FlightSegment::where('flight_booking_id', $flightBookingDetails->id)->get();
        $flightPassangers = FlightPassanger::where('flight_booking_id', $flightBookingDetails->id)->get();
        return view('booking.details', compact('flightBookingDetails', 'flightSegments', 'flightPassangers', 'bookingResSegs'));
    }

    public function cancelFlightBooking($booking_no){

        $flightBookingInfo = FlightBooking::where('booking_no', $booking_no)->first();

        // sabre
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1){
            $cancelResponse = json_decode(SabreFlightBooking::cancelBooking($booking_no), true);
            if(isset($cancelResponse['booking']['bookingId']) && $cancelResponse['booking']['bookingId'] == $flightBookingInfo->pnr_id){
                FlightBooking::where('pnr_id', $flightBookingInfo->pnr_id)->update([
                    'status' => 3,
                    'booking_cancelled_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Toastr::success('Flight Booking Cancelled Successfully', 'Cancelled');
                return back();
            } else {
                Toastr::error('Something Went Wrong', 'Try Again Later');
                return back();
            }
        }

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $ticketCancelResponse = json_decode(FlyhubFlightTicketIssue::cancelTicket($flightBookingInfo), true);
            if($ticketCancelResponse['status'] == 'success'){
                $flightBookingInfo->status = 3;
                $flightBookingInfo->booking_cancelled_at = Carbon::now();
                $flightBookingInfo->save();

                Toastr::success('Flight Booking Cancelled Successfully', 'Cancelled');
                return back();
            } else {
                Toastr::error('Failed to Cancel Booking', 'Failed');
                return back();
            }
        }
    }

    public function cancelIssuedTicket($booking_no){
        if(Auth::user()->ticket_status == 0){
            Toastr::error('Ticketing Related Permission Denied');
            return back();
        }

        $flightBookingInfo = FlightBooking::where('booking_no', $booking_no)->first();

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $ticketCancelResponse = json_decode(FlyhubFlightTicketIssue::cancelTicket($flightBookingInfo), true);
            if($ticketCancelResponse['status'] == 'success'){
                $flightBookingInfo->status = 4;
                $flightBookingInfo->ticket_cancelled_at = Carbon::now();
                $flightBookingInfo->save();

                Toastr::success('Flight Ticket Cancelled Successfully', 'Cancelled');
                return back();
            } else {
                Toastr::error('Failed to Cancel Ticket', 'Failed');
                return back();
            }
        }
    }

    public function bookingPreview($bookingNo){
        $flightBookingDetails = FlightBooking::where('booking_no', $bookingNo)->first();
        $flightSegments = FlightSegment::where('flight_booking_id', $flightBookingDetails->id)->get();
        $flightPassangers = FlightPassanger::where('flight_booking_id', $flightBookingDetails->id)->get();
        $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();

        $bookingResSegs = null;
        if($flightBookingDetails->booking_response){
            $bookingRes = json_decode($flightBookingDetails->booking_response, true);
            $bookingResSegs = $bookingRes['CreatePassengerNameRecordRS']['TravelItineraryRead']['TravelItinerary']['ItineraryInfo']['ReservationItems']['Item'];
        }

        $pdf = Pdf::loadView('booking.preview', compact('flightBookingDetails', 'flightSegments', 'flightPassangers', 'companyProfile', 'bookingResSegs'));
        return $pdf->stream($flightBookingDetails->booking_no.'.pdf');
    }

    public function issueFlightTicket($booking_no){

        if(Auth::user()->ticket_status == 0){
            Toastr::error('Ticket Issue Permission Denied');
            return back();
        }

        $flightBookingInfo = FlightBooking::where('booking_no', $booking_no)->first();
        $base_fare_amount = $flightBookingInfo->base_fare_amount;

        if(Auth::user()->user_type == 2){ //if b2b user then check balance
            if(Auth::user()->balance < ( $base_fare_amount - (($base_fare_amount*Auth::user()->comission)/100) )){
                Toastr::error('Not Enough Balance', 'Please Recharge');
                return back();
            }
        }

        // sabre ticket issue
        $sabreGds = Gds::where('code', 'sabre')->first();
        if($sabreGds->status == 1 && $flightBookingInfo->gds == 'Sabre'){
            $ticketIssueResponse = json_decode(SabreFlightTicketIssue::issueTicket($flightBookingInfo->pnr_id), true);
            if(isset($ticketIssueResponse['AirTicketRS']['ApplicationResults']['status']) && $ticketIssueResponse['AirTicketRS']['ApplicationResults']['status'] == 'Complete'){

                if(Auth::user()->user_type == 2){
                    $user = User::where('id', Auth::user()->id)->first();
                    $user->balance = $user->balance - ($base_fare_amount - (($base_fare_amount*Auth::user()->comission)/100));
                    $user->save();
                }

                $flightBookingInfo->status = 2;
                $flightBookingInfo->ticketing_response = json_encode($ticketIssueResponse, true);
                $flightBookingInfo->ticket_issued_at = Carbon::now();
                $flightBookingInfo->save();
                return redirect('view/issued/tickets');
            } else {

                $flightBookingInfo->ticketing_response = json_encode($ticketIssueResponse, true);
                $flightBookingInfo->save();

                Toastr::error('Failed to issue Ticket', 'Failed');
                return back();
            }
        }

        // flyhub
        $flyhubGds = Gds::where('code', 'flyhub')->first();
        if($flyhubGds->status == 1){
            $ticketIssueResponse = json_decode(FlyhubFlightTicketIssue::issueTicket($flightBookingInfo), true);
            if($ticketIssueResponse['status'] == 'success'){
                $flightBookingInfo->status = 2;
                $flightBookingInfo->ticket_issued_at = Carbon::now();
                $flightBookingInfo->ticketing_response = json_encode($ticketIssueResponse, true);
                $flightBookingInfo->save();
                return redirect('view/issued/tickets');
            } else {
                Toastr::error('Failed to issue Ticket', 'Failed');
                return back();
            }
        }

    }

    public function viewIssuedTickets(Request $request){
        if ($request->ajax()) {

            // removing log coloumns
            $columns = Schema::getColumnListing('flight_bookings');
            $excluded = ['booking_request', 'booking_response', 'get_booking_response', 'ticketing_response'];
            $columns = array_diff($columns, $excluded);
            $columns = array_map(function ($col) {
                return "flight_bookings.$col";
            }, $columns);

            if(Auth::user()->user_type == 1){

                $query = DB::table('flight_bookings')
                        ->leftJoin('users', 'flight_bookings.booked_by', '=', 'users.id')
                        ->select([...$columns, 'users.name as b2b_user'])
                        ->where('flight_bookings.status', 2)
                        ->where('departure_date', '>=', Carbon::today()->toDateString())
                        ->orderBy('flight_bookings.id', 'desc');

            } else {
                $query = FlightBooking::where('booked_by', Auth::user()->id)
                                    ->where('status', 2)
                                    ->where('departure_date', '>=', Carbon::today()->toDateString())
                                    ->orderBy('id', 'desc');
            }

            return Datatables::of($query)
                    ->addColumn('flight_routes', function($data){
                        $routeString = $data->departure_location." - ".$data->arrival_location;
                        if($data->flight_type == 2){
                            $routeString .= " - ".$data->departure_location;
                        }
                        return $routeString;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->editColumn('total_fare', function($data) {
                        return $data->currency." ".number_format($data->total_fare);
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Booked</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:green'>Issued</span>";
                        if($data->status == 3)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";
                        if($data->status == 4)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";

                    })
                    ->addColumn('total_passangers', function($data){
                        return $data->adult+$data->child+$data->infant;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('flight/booking/details')."/".$data->booking_no.'" class="btn-sm btn-info text-white rounded d-inline-block mb-1"><i class="fas fa-eye"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Cancel" class="btn-sm btn-danger rounded d-inline-block cancelBtn"><i class="fas fa-times-circle"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('booking.issued_ticket');
    }

    public function archivedIssuedTickets(Request $request){
        if ($request->ajax()) {

            // removing log coloumns
            $columns = Schema::getColumnListing('flight_bookings');
            $excluded = ['booking_request', 'booking_response', 'get_booking_response', 'ticketing_response'];
            $columns = array_diff($columns, $excluded);
            $columns = array_map(function ($col) {
                return "flight_bookings.$col";
            }, $columns);

            if(Auth::user()->user_type == 1){

                $query = DB::table('flight_bookings')
                            ->leftJoin('users', 'flight_bookings.booked_by', '=', 'users.id')
                            ->select([...$columns, 'users.name as b2b_user'])
                            ->where('flight_bookings.status', 2)
                            ->where('departure_date', '<', Carbon::today()->toDateString())
                            ->orderBy('flight_bookings.id', 'desc');

            } else {

                $query = FlightBooking::where('booked_by', Auth::user()->id)
                                    ->where('status', 2)
                                    ->where('departure_date', '<', Carbon::today()->toDateString())
                                    ->orderBy('id', 'desc');

            }

            return Datatables::of($query)
                    ->addColumn('flight_routes', function($data){
                        $routeString = $data->departure_location." - ".$data->arrival_location;
                        if($data->flight_type == 2){
                            $routeString .= " - ".$data->departure_location;
                        }
                        return $routeString;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->editColumn('total_fare', function($data) {
                        return $data->currency." ".number_format($data->total_fare);
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Booked</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:green'>Issued</span>";
                        if($data->status == 3)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";
                        if($data->status == 4)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";

                    })
                    ->addColumn('total_passangers', function($data){
                        return $data->adult+$data->child+$data->infant;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('flight/booking/details')."/".$data->booking_no.'" class="btn-sm btn-info text-white rounded d-inline-block mb-1"><i class="fas fa-eye"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Cancel" class="btn-sm btn-danger rounded d-inline-block cancelBtn"><i class="fas fa-times-circle"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('booking.archived_issued_tickets');
    }

    public function viewCancelledTickets(Request $request){
        if ($request->ajax()) {

            // removing log coloumns
            $columns = Schema::getColumnListing('flight_bookings');
            $excluded = ['booking_request', 'booking_response', 'get_booking_response', 'ticketing_response'];
            $columns = array_diff($columns, $excluded);
            $columns = array_map(function ($col) {
                return "flight_bookings.$col";
            }, $columns);


            if(Auth::user()->user_type == 1){

                $query = DB::table('flight_bookings')
                        ->leftJoin('users', 'flight_bookings.booked_by', '=', 'users.id')
                        ->select([...$columns, 'users.name as b2b_user'])
                        ->where('flight_bookings.status', 4)
                        ->orderBy('flight_bookings.id', 'desc');

            } else {

                $query = FlightBooking::where('booked_by', Auth::user()->id)
                                    ->where('status', 4)
                                    ->select([...$columns])
                                    ->orderBy('id', 'desc');
            }

            return Datatables::of($query)
                    ->addColumn('flight_routes', function($data){
                        $routeString = $data->departure_location." - ".$data->arrival_location;
                        if($data->flight_type == 2){
                            $routeString .= " - ".$data->departure_location;
                        }
                        return $routeString;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i a", strtotime($data->created_at));
                    })
                    ->editColumn('total_fare', function($data) {
                        return $data->currency." ".number_format($data->total_fare);
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Booked</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:green'>Issued</span>";
                        if($data->status == 3)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";
                        if($data->status == 4)
                            return "<span style='font-weight:600; color:red'>Cancelled</span>";

                    })
                    ->addColumn('total_passangers', function($data){
                        return $data->adult+$data->child+$data->infant;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('flight/booking/details')."/".$data->booking_no.'" class="btn-sm btn-info text-white rounded d-inline-block mb-1"><i class="fas fa-eye"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Cancel" class="btn-sm btn-danger rounded d-inline-block cancelBtn"><i class="fas fa-times-circle"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('booking.cancelled_ticket');
    }

    public function updatePnrBooking(Request $request){
        FlightBooking::where('booking_no', $request->booking_no)->update([
            'pnr_id' => $request->pnr_id,
            'status' => $request->status,
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('Flight Booked Successfully', 'Successful');
        return back();
    }
}
