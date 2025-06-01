<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlightBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\FlightBookingResource;
use App\Models\SabreFlightBooking;

class FlightController extends Controller
{
    public function bookMyFlight(Request $request)
    {
        if ($request->header('Authorization-Header') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $request->validate([

                'flight_type' => 'required|integer',
                'pnr_id' => 'required|string',
                'traveller_name' => 'required|string',
                'traveller_email' => 'required|email',
                'traveller_contact' => 'required|string',
                'departure_date' => 'required|string',
                'departure_location' => 'required|string',
                'arrival_location' => 'required|string',
                'governing_carriers' => 'required|string',
                'currency' => 'required|string',
                'last_ticket_datetime' => 'required|string',
                'adult' => 'required|integer',
                'child' => 'required|integer',
                'infant' => 'required|integer',

                'segment_array' => 'required|array|min:1',

                'passenger_data' => 'required|array|min:1',
                'passenger_data.*.first_name' => 'required|string',
                'passenger_data.*.last_name' => 'required|string',
                'passenger_data.*.title' => 'nullable|string',
                'passenger_data.*.passanger_type' => 'required|string',
                'passenger_data.*.email' => 'required|email',
                'passenger_data.*.phone' => 'required|string',
                'passenger_data.*.dob' => 'required|date',
                'passenger_data.*.age' => 'required',
                'passenger_data.*.document_type' => 'required|string',
                'passenger_data.*.document_no' => 'required|string',
                'passenger_data.*.document_expire_date' => 'required|date',
                'passenger_data.*.document_issue_country' => 'required|string',
                'passenger_data.*.nationality' => 'required|string',

            ]);

            $now = Carbon::now();
            $user = $request->user();

            $flightBookingId = DB::table('flight_bookings')->insertGetId([
                'flight_type' => $request->flight_type, // 1 = one way, 2 = round trip
                'booking_no' => str::random(3) . "-" . time(),
                "source" => 3,
                'passanger_id' => $user->id,
                'booked_by' => null,
                'b2b_comission' => 0,
                'pnr_id' => $request->pnr_id,
                'airlines_pnr' => $request->pnr_id,
                'gds' => "Sabre",
                'gds_unique_id' => "SOOL",
                'traveller_name' => $request->traveller_name,
                'traveller_email' => $request->traveller_email,
                'traveller_contact' => $request->traveller_contact,
                'departure_date' => $request->departure_date,
                'departure_location' => $request->departure_location,
                'arrival_location' => $request->arrival_location,
                'governing_carriers' => $request->governing_carriers,
                'adult' => (string) $request->adult,
                'child' => (string) $request->child,
                'infant' => (string) $request->infant,
                'base_fare_amount' => $request->base_fare_amount,
                'total_tax_amount' => $request->total_tax_amount,
                'total_fare' => $request->total_fare,
                'currency' => $request->currency,
                'last_ticket_datetime' => $request->last_ticket_datetime,
                'booking_request' => null,
                'booking_response' => null,
                'status' => 1,
                'payment_status' => 0,
                'is_live' => $request->is_live,
                'created_at' => $now
            ]);


            foreach ($request->segment_array as $segment) {
                DB::table('flight_segments')->insert([

                    'flight_booking_id' => $flightBookingId,
                    'total_miles_flown' => $segment['totalMilesFlown'],
                    'elapsed_time' => $segment['elapsedTime'],
                    'booking_code' => $segment['bookingCode'],
                    'cabin_code' => $segment['cabinCode'],
                    'baggage_allowance' => $segment['baggageAllowance'],

                    'departure_airport_code' => $segment['departure']['airport'],
                    'departure_city_code' => $segment['departure']['city'],
                    'departure_country_code' => $segment['departure']['country'],
                    'departure_time' => $segment['departure']['time'],
                    'departure_terminal' => $segment['departure']['terminal'] ?? null,

                    'arrival_airport_code' => $segment['arrival']['airport'],
                    'arrival_city_code' => $segment['arrival']['city'],
                    'arrival_country_code' => $segment['arrival']['country'],
                    'arrival_time' => $segment['arrival']['time'],
                    'arrival_terminal' => $segment['arrival']['terminal'] ?? null,

                    'carrier_marketing_code' => $segment['carrier']['marketing'],
                    'carrier_marketing_flight_number' => $segment['carrier']['marketingFlightNumber'],
                    'carrier_operating_code' => $segment['carrier']['operating'],
                    'carrier_operating_flight_number' => $segment['carrier']['operatingFlightNumber'],
                    'carrier_equipment_code' => $segment['carrier']['equipment']['code'],

                    'created_at' => $now
                ]);
            }

            foreach ($request->passenger_data as $passenger) {
                DB::table('flight_passangers')->insert([
                    'flight_booking_id' => $flightBookingId,
                    'passanger_type' => $passenger['passanger_type'],
                    'title' => $passenger['title'],
                    'first_name' => $passenger['first_name'],
                    'last_name' => $passenger['last_name'],
                    'email' => $passenger['email'],
                    'phone' => $passenger['phone'],
                    'dob' => $passenger['dob'],
                    'age' => str_pad($passenger['age'], 2, "0", STR_PAD_LEFT),
                    'document_type' => $passenger['document_type'],
                    'document_no' => $passenger['document_no'],
                    'document_expire_date' => $passenger['document_expire_date'],
                    'document_issue_country' => $passenger['document_issue_country'],
                    'nationality' => $passenger['nationality'],
                    'frequent_flyer_no' => $passenger['frequent_flyer_no'] ?? null,
                    'created_at' => now()
                ]);
            }

            $flightBooking = FlightBooking::where('id', $flightBookingId)->first();

            return response()->json([
                'success' => true,
                'message' => 'Flight booking completed successfully.',
                'data' => new FlightBookingResource($flightBooking)
            ]);


        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function flightBookingPayment(Request $request){
        if ($request->header('Authorization-Header') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $request->validate([
                'flight_booking_id' => 'required|integer',
                'payment_method' => 'required|integer',
                'transaction_id' => 'required|string',
            ]);

            $flightBooking = FlightBooking::where('id', $request->flight_booking_id)->first();

            if ($flightBooking) {
                $flightBooking->update([
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'payment_status' => 1,
                    'updated_at' => Carbon::now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Payment status updated successfully.",
                    'data' => new FlightBookingResource($flightBooking)
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Flight booking not found.",
                    'data' => null
                ], 404);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function myFlightBookings(Request $request){
        if ($request->header('Authorization-Header') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $flightBookings = FlightBooking::where('passanger_id', $request->user()->id)->orderBy('id', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => "Flight bookings retrieved successfully.",
                'data' => FlightBookingResource::collection($flightBookings)->resource
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function flightBookingDetails(Request $request){
        if ($request->header('Authorization-Header') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $flightBooking = FlightBooking::where('booking_no', $request->booking_no)->where('passanger_id', $request->user()->id)->first();
            if($flightBooking){
                return response()->json([
                    'success' => true,
                    'message' => "Flight booking details retrieved successfully.",
                    'data' => new FlightBookingResource($flightBooking)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Flight booking not found.",
                    'data' => null
                ], 404);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function cancelFlightBooking(Request $request){
        if ($request->header('Authorization-Header') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $flightBooking = FlightBooking::where('booking_no', $request->booking_no)->where('passanger_id', $request->user()->id)->first();
            if($flightBooking){
                if($flightBooking->status == 1){
                    $cancelResponse = json_decode(SabreFlightBooking::cancelBooking($flightBooking->booking_no), true);
                    if(isset($cancelResponse['booking']['bookingId']) && $cancelResponse['booking']['bookingId'] == $flightBooking->pnr_id){
                        FlightBooking::where('id', $flightBooking->id)->update([
                            'status' => 3,
                            'booking_cancelled_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => "Flight booking cancelled successfully.",
                            'data' => new FlightBookingResource($flightBooking)
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => "Contact Support for cancellation",
                            'data' => new FlightBookingResource($flightBooking)
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Contact Support for cancellation",
                        'data' => new FlightBookingResource($flightBooking)
                    ]);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Flight booking not found.",
                    'data' => null
                ], 404);
            }


        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }
}
