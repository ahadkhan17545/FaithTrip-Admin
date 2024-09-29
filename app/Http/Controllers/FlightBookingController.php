<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use App\Models\SavedPassanger;
use Yajra\DataTables\DataTables;
use App\Models\FlightPassanger;
use App\Models\FlightSegment;
use App\Models\SabreFlightBooking;
use App\Models\SabreFlightTicketIssue;
use App\Models\SabreGdsConfig;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FlightBookingController extends Controller
{
    public function bookFlightWithPnr(Request $request){

        $revlidatedData = session('revlidatedData');

        if(isset($request->first_name[0]) && $request->first_name[0] && isset($request->last_name[0]) && $request->last_name[0] && $request->traveller_contact && $request->traveller_email && isset($request->titles[0]) && $request->titles[0] && isset($request->dob[0]) && $request->dob[0]){

            $onlineBookingInfo = json_decode(SabreFlightBooking::flightBooking($revlidatedData, $request->traveller_contact, $request->first_name, $request->last_name, $request->titles, $request->dob[0], $request->passanger_type, $request->traveller_email), true);

        } else {
            Toastr::error('Passanger Information Missing', 'Failed');
            return redirect('/home');
        }

        // echo "<pre>";
        // echo SabreFlightBooking::flightBooking($revlidatedData, $request->traveller_contact, $request->first_name, $request->last_name, $request->titles, $request->dob[0], $request->passanger_type, $request->traveller_email);
        // echo "</pre>";
        // exit();

        $bookinPnrID = null;
        if(isset($onlineBookingInfo['CreatePassengerNameRecordRS']['ApplicationResults']['status']) && $onlineBookingInfo['CreatePassengerNameRecordRS']['ApplicationResults']['status'] == 'Complete'){
            $bookinPnrID = $onlineBookingInfo['CreatePassengerNameRecordRS']['ItineraryRef']['ID'];
            $status = 1;
        } else{
            $status = 0;
        }

        DB::transaction(function () use ($request, $bookinPnrID, $status) {

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

            $flightBookingId = FlightBooking::insertGetId([
                'booking_no' => str::random(3) . "-" . time(),
                'booked_by' => Auth::user()->id,
                'b2b_comission' => Auth::user()->comission,
                'pnr_id' => $bookinPnrID,
                'gds' => $request->gds,
                'gds_unique_id' => $request->gds_unique_id,
                'traveller_name' => $request->traveller_name,
                'traveller_email' => $request->traveller_email,
                'traveller_contact' => $request->traveller_contact,
                'departure_date' => $request->departure_date,
                'departure_location' => $request->departure_location,
                'arrival_location' => $request->arrival_location,
                'governing_carriers' => $request->governing_carriers,
                'adult' => session('adult'),
                'child' => session('child'),
                'infant' => session('infant'),
                'base_fare_amount' => $base_fare_amount,
                'total_tax_amount' => $total_tax_amount,
                'total_fare' => $total_fare,
                'currency' => $request->currency,
                'last_ticket_datetime' => $request->last_ticket_datetime,
                'status' => $status,
                'is_live' => $sabreGdsInfo ? $sabreGdsInfo->is_production : 0,
                'created_at' => Carbon::now()
            ]);


            $segmentArray = [];
            $legsArray = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['legs'];
            foreach ($legsArray as $key => $leg) {
                $legRef = $leg['ref'] - 1;
                $legDescription = $revlidatedData['groupedItineraryResponse']['legDescs'][$legRef];
                $schedulesArray = $legDescription['schedules'];
                foreach ($schedulesArray as $schedule) {
                    $scheduleRef = $schedule['ref'] - 1;
                    $segmentArray[] = $revlidatedData['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
                }
            }


            foreach ($segmentArray as $segmentIndex => $segmentData){

                if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'])){
                    $bookingCode = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'];
                }
                if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['cabinCode'])){
                    $cabinCode = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['cabinCode'];
                }

                $baggageAllowanceRef = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['baggageInformation'][0]['allowance']['ref'];
                $baggageAllowanceDescs = $revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageAllowanceRef-1];
                if(isset($baggageAllowanceDescs['weight'])){
                    $baggageAllowance = $baggageAllowanceDescs['weight']." ".$baggageAllowanceDescs['unit'];
                } else if(isset($baggageAllowance['pieceCount'])){
                    $baggageAllowance = $baggageAllowanceDescs['pieceCount']." Pieces";
                } else{
                    $baggageAllowance = null;
                }

                FlightSegment::insert([
                    'flight_booking_id' => $flightBookingId,
                    'total_miles_flown' => $segmentData['totalMilesFlown'],
                    'elapsed_time' => $segmentData['elapsedTime'],
                    'booking_code' => $bookingCode,
                    'cabin_code' => $cabinCode,
                    'baggage_allowance' => $baggageAllowance,
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

                if($passangerIndex == 0 && $request->save_passanger){

                    $savedPassanger = SavedPassanger::where('contact', $request->traveller_contact)->first();
                    if(!$savedPassanger){
                        $savedPassanger = new SavedPassanger();
                    }

                    $savedPassanger->saved_by = Auth::user()->id;
                    $savedPassanger->email = $request->traveller_email;
                    $savedPassanger->contact = $request->traveller_contact;
                    $savedPassanger->type = $request->passanger_type[$passangerIndex];
                    $savedPassanger->title = $request->titles[$passangerIndex];
                    $savedPassanger->first_name = $firstName;
                    $savedPassanger->last_name = $request->last_name[$passangerIndex];
                    $savedPassanger->dob = $request->dob[$passangerIndex];
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
                    'dob' => $request->dob[$passangerIndex],
                    'document_type' => $request->document_type[$passangerIndex],
                    'document_no' => $request->document_no[$passangerIndex],
                    'document_expire_date' => $request->document_expire_date[$passangerIndex],
                    'document_issue_country' => $request->document_issue_country[$passangerIndex],
                    'nationality' => $request->nationality[$passangerIndex],
                    'frequent_flyer_no' => $request->frequent_flyer_no[$passangerIndex],
                    'created_at' => Carbon::now()
                ]);
            }

        }, 5);

        session()->forget(['adult', 'child', 'infant', 'revlidatedData']);

        if($status == 0){
            Toastr::success('Flight Booking Request Sent', 'Success');
            return redirect('/view/all/booking');
        } else {
            Toastr::success('Flight Booked Successfully', 'Success');
            return redirect('/view/all/booking');
        }

    }

    public function viewAllBooking(Request $request){

        if ($request->ajax()) {

            if(Auth::user()->user_type == 1){
                $data = FlightBooking::where('status', 1)->orWhere('status', 0)->orderBy('id', 'desc')->get();
            } else {
                $data = FlightBooking::where('booked_by', Auth::user()->id)->where('status', 1)->orWhere('status', 0)->orderBy('id', 'desc')->get();
            }

            return Datatables::of($data)
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

            if(Auth::user()->user_type == 1){
                $data = FlightBooking::where('status', 3)->orderBy('id', 'desc')->get();
            } else {
                $data = FlightBooking::where('booked_by', Auth::user()->id)->where('status', 3)->orderBy('id', 'desc')->get();
            }

            return Datatables::of($data)
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
        $flightSegments = FlightSegment::where('flight_booking_id', $flightBookingDetails->id)->get();
        $flightPassangers = FlightPassanger::where('flight_booking_id', $flightBookingDetails->id)->get();
        return view('booking.details', compact('flightBookingDetails', 'flightSegments', 'flightPassangers'));
    }

    public function cancelFlightBooking($pnrId){

        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){

            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");

            if($currentDate >= $tokenExpireDate){
                FlightSearchController::generateAccessToken();
            }

        } else {
            FlightSearchController::generateAccessToken();
        }

        $data = array(
            "confirmationId" => $pnrId,
            "retrieveBooking" => true,
            "cancelAll" => true,
            "errorHandlingPolicy" => "ALLOW_PARTIAL_CANCEL"
        );
        $payload = json_encode($data);

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v1/trip/orders/cancelBooking';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v1/trip/orders/cancelBooking';
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
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Conversation-ID: 2021.01.DevStudio',
                'Authorization: Bearer '. session('access_token'),
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $cancelResponse = json_decode($response, true);
        if(isset($cancelResponse['booking']['bookingId']) && $cancelResponse['booking']['bookingId'] == $pnrId){
            FlightBooking::where('pnr_id', $pnrId)->update([
                'status' => 3,
                'booking_cancelled_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            Toastr::error('Something Went Wrong', 'Try Again Later');
            return back();
        }

        Toastr::success('Flight Booking Cancelled Successfully', 'Cancelled');
        return back();
    }

    public function bookingPreview($bookingNo){
        $flightBookingDetails = FlightBooking::where('booking_no', $bookingNo)->first();
        $flightSegments = FlightSegment::where('flight_booking_id', $flightBookingDetails->id)->get();
        $flightPassangers = FlightPassanger::where('flight_booking_id', $flightBookingDetails->id)->get();
        return view('booking.preview', compact('flightBookingDetails', 'flightSegments', 'flightPassangers'));
    }

    public function issueFlightTicket($pnrId){

        if(Auth::user()->ticket_status == 0){
            Toastr::error('Ticket Issue Permission Denied');
            return back();
        }

        $flightBookingInfo = FlightBooking::where('pnr_id', $pnrId)->first();

        if(Auth::user()->user_type == 2){ //if b2b user then check balance
            $base_fare_amount = $flightBookingInfo->base_fare_amount;
            if(Auth::user()->balance < ( $base_fare_amount - (($base_fare_amount*Auth::user()->comission)/100) )){
                Toastr::error('Not Enough Balance', 'Please Recharge');
                return back();
            }
        }

        $ticketIssueResponse = json_decode(SabreFlightTicketIssue::issueTicket($pnrId), true);

        // echo "<pre>";
        // print_r($ticketIssueResponse);
        // echo "</pre>";
        // exit();

        if(isset($ticketIssueResponse['AirTicketRS']['ApplicationResults']['status']) && $ticketIssueResponse['AirTicketRS']['ApplicationResults']['status'] == 'Complete'){

            $flightBookingInfo->status = 2;
            $flightBookingInfo->ticket_issued_at = Carbon::now();
            $flightBookingInfo->save();
            return redirect('view/issued/tickets');

        } else {
            Toastr::success('Ticket Issued Successfully', 'Successful');
            return back();
        }

    }

    public function viewIssuedTickets(Request $request){
        if ($request->ajax()) {

            if(Auth::user()->user_type == 1){
                $data = FlightBooking::where('status', 2)->orderBy('id', 'desc')->get();
            } else {
                $data = FlightBooking::where('booked_by', Auth::user()->id)->where('status', 2)->orderBy('id', 'desc')->get();
            }

            return Datatables::of($data)
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
    public function viewCancelledTickets(Request $request){
        if ($request->ajax()) {

            if(Auth::user()->user_type == 1){
                $data = FlightBooking::where('status', 4)->orderBy('id', 'desc')->get();
            } else {
                $data = FlightBooking::where('booked_by', Auth::user()->id)->where('status', 4)->orderBy('id', 'desc')->get();
            }

            return Datatables::of($data)
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
