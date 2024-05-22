@extends('master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0" style="font-size: 18px">Flight Booking Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <b class="d-block mb-1" style="font-size: 16px"><u>Booking Info</u></b>
                            <table>
                                <tr>
                                    <th>Booking No </th>
                                    <td>: <i>{{$flightBookingDetails->booking_no}}</i></td>
                                </tr>
                                <tr>
                                    <th>Booking Date </th>
                                    <td>: <i>{{date("Y-m-d h:i a", strtotime($flightBookingDetails->created_at))}}</i></td>
                                </tr>
                                <tr>
                                    <th>Booked By </th>
                                    <td>: <i>{{Auth::user()->name}}</i></td>
                                </tr>
                                <tr>
                                    <th>GDS </th>
                                    <td>: <i>{{$flightBookingDetails->gds}}</i></td>
                                </tr>
                                <tr>
                                    <th>GDS ID </th>
                                    <td>: <i>{{$flightBookingDetails->gds_unique_id}}</i></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <b class="d-block mb-1" style="font-size: 16px"><u>Traveller Info</u></b>
                            <table>
                                <tr>
                                    <th>PNR ID </th>
                                    <td>: <i>{{$flightBookingDetails->pnr_id}}</i></td>
                                </tr>
                                <tr>
                                    <th>Name </th>
                                    <td>: <i>{{$flightBookingDetails->traveller_name}}</i></td>
                                </tr>
                                <tr>
                                    <th>Email </th>
                                    <td>: <i>{{$flightBookingDetails->traveller_email}}</i></td>
                                </tr>
                                <tr>
                                    <th>Contact </th>
                                    <td>: <i>{{$flightBookingDetails->traveller_contact}}</i></td>
                                </tr>
                                <tr>
                                    <th>Total Passanger </th>
                                    <td>: <i>@if($flightBookingDetails->adult){{$flightBookingDetails->adult}} Adult @endif @if($flightBookingDetails->child){{$flightBookingDetails->child}} Child @endif @if($flightBookingDetails->infant){{$flightBookingDetails->infant}} Infant @endif</i></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <b class="d-block mb-1" style="font-size: 16px"><u>Flight Info</u></b>
                            <table>
                                <tr>
                                    <th>Departure </th>
                                    <td>: <i>{{$flightBookingDetails->departure_date}}</i></td>
                                </tr>
                                <tr>
                                    <th>From </th>
                                    <td>: <i>{{$flightBookingDetails->departure_location}}</i></td>
                                </tr>
                                <tr>
                                    <th>To </th>
                                    <td>: <i>{{$flightBookingDetails->arrival_location}}</i></td>
                                </tr>
                                <tr>
                                    <th>Total Fare </th>
                                    <td>: <i>{{$flightBookingDetails->currency}} {{number_format($flightBookingDetails->total_fare)}}</i></td>
                                </tr>
                                <tr>
                                    <th>Status </th>
                                    <td>:</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>

@endsection
