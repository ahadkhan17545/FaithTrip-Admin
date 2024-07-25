<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function flightBookingReport(){
        $users = DB::table('users')->get();
        return view('report.flight_booking', compact('users'));
    }
    public function generateFlightBookingReport(Request $request){

        $startDate = $request->start_date != '' ? date("Y-m-d", strtotime(str_replace("/","-",$request->start_date)))." 00:00:00" : '';
        $endDate = $request->end_date != '' ? date("Y-m-d", strtotime(str_replace("/","-",$request->end_date)))." 23:59:59" : '';
        $travelDate = $request->travel_date;

        if(Auth::user()->user_type == 1){
            $bookedBy = $request->booked_by;
        } else {
            $bookedBy = Auth::user()->id;
        }

        $bookingStatus = $request->booking_status;
        $pnrId = $request->pnr_id;

        $query = DB::table('flight_bookings')
                    ->leftJoin('users', 'flight_bookings.booked_by', 'users.id')
                    ->select('flight_bookings.*', 'users.name as b2b_user_name');

        if ($startDate != '') {
            $query->where('flight_bookings.created_at', '>=', $startDate);
        }
        if ($endDate != '') {
            $query->where('flight_bookings.created_at', '<=', $endDate);
        }
        if ($travelDate != '') {
            $query->where('departure_date', $travelDate);
        }
        if ($bookedBy != '') {
            $query->where('booked_by', $bookedBy);
        }
        if ($bookingStatus != '') {
            $query->where('flight_bookings.status', $bookingStatus);
        }
        if ($pnrId != '') {
            $query->where('pnr_id', $pnrId);
        }
        $data = $query->orderBy('flight_bookings.id', 'desc')->get();

        $returnHTML = view('report.flight_booking_view', compact('data', 'startDate', 'endDate'))->render();
        return response()->json(['report' => $returnHTML]);

    }

    public function b2bFinancialReport(){
        $users = DB::table('users')->get();
        return view('report.b2b_financial', compact('users'));
    }

    public function generateB2bFinancialReport(Request $request){

        $startDate = $request->start_date != '' ? date("Y-m-d", strtotime(str_replace("/","-",$request->start_date)))." 00:00:00" : '';
        $endDate = $request->end_date != '' ? date("Y-m-d", strtotime(str_replace("/","-",$request->end_date)))." 23:59:59" : '';
        $userId = $request->user_id;
        $userStatus = $request->user_status;

        $query = DB::table('users')
                    ->leftJoin('company_profiles', 'company_profiles.user_id', 'users.id')
                    ->select('users.*', 'company_profiles.name as company_name');

        if ($userId != '') {
            $query->where('users.id', $userId);
        }
        if ($userStatus != '') {
            $query->where('users.status', $userStatus);
        }
        $data = $query->orderBy('users.name', 'asc')->orderBy('company_profiles.name', 'asc')->get();

        $returnHTML = view('report.b2b_financial_view', compact('data', 'startDate', 'endDate'))->render();
        return response()->json(['report' => $returnHTML]);

    }
}
