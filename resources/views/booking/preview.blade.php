<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            color: #1e1e1e;
        }
        table.booking_info{
            width: 100%;
            padding: 0;
            margin: 0;
            margin-top: 12px;
            border-collapse: collapse;
        }
        table.booking_info tr td{
            padding-bottom: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <table border="0" style="width: 100%; padding: 0px; margin: 0; border-collapse: collapse; border-bottom: 2px solid gray;">
        <tr>
            <td style="width: 70%; padding-bottom: 10px;">
                <h1 style="margin: 0; font-size: 18px; font-weight: 600; margin-bottom: 6px; color: darkslategray;">{{$companyProfile->name}}</h1>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;">{{$companyProfile->address}}</p>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;"><strong>Email:</strong> {{$companyProfile->email}}</p>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;"><strong>Phone:</strong> {{$companyProfile->phone}}</p>
            </td>
            <td style="width: 30%; text-align: right; padding-bottom: 10px;">
                <img src="{{public_path($companyProfile->logo)}}" alt="Company Logo" style="width: 80px; height: auto;">
            </td>
        </tr>
    </table>

    <table class="booking_info" border="0">
        <tr>
            <td style="width: 52%;"><strong>Booking Date:</strong> {{ date('Y-m-d h:i a', strtotime($flightBookingDetails->created_at)) }}</td>
            <td style="width: 48%;"><strong>Traveller Name:</strong> {{ $flightBookingDetails->traveller_name }}</td>
        </tr>
        <tr>
            <td><strong>Booking No:</strong> {{ $flightBookingDetails->booking_no }}</td>
            <td><strong>Traveller Email:</strong> {{ $flightBookingDetails->traveller_email }}</td>
        </tr>
        <tr>
            <td><strong>PNR ID:</strong> {{ $flightBookingDetails->pnr_id }}</td>
            <td><strong>Traveller Contact:</strong> {{ $flightBookingDetails->traveller_contact }}</td>
        </tr>
        <tr>
            <td><strong>Ticket No:</strong> {{ $flightBookingDetails->ticket_id }}</td>
            <td>
                <strong>Total Passangers:</strong>
                @if ($flightBookingDetails->adult)
                    {{ $flightBookingDetails->adult }} Adult
                @endif
                @if ($flightBookingDetails->child)
                    {{ $flightBookingDetails->child }} Child
                @endif
                @if ($flightBookingDetails->infant)
                    {{ $flightBookingDetails->infant }} Infant
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Flight Route:</strong> {{ $flightBookingDetails->departure_location }} - {{ $flightBookingDetails->arrival_location }} @if($flightBookingDetails->flight_type == 2) - {{ $flightBookingDetails->departure_location }} @endif</td>
            <td>
                <strong>Departure Date:</strong>
                @php
                    $departure = $bookingResSegs ? $bookingResSegs[0]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                    $departureDateTime = explode('T', $departure);
                @endphp
                {{date('j M Y', strtotime($departureDateTime[0]))}} {{substr($departureDateTime[1], 0, 5)}}
            </td>
        </tr>
        <tr>
            <td><strong>Flight Type:</strong>
                @if($flightBookingDetails->flight_type == 2)
                    Round Trip
                @else
                    One Way
                @endif
            </td>
            <td>
                <strong>Status:</strong>
                @if ($flightBookingDetails->status == 0)
                    <span style="color: goldenrod; font-weight: 600">Booking Requested</span>
                @endif
                @if ($flightBookingDetails->status == 1)
                    <span style="color: green; font-weight: 600">Booking Done</span>
                @endif
                @if ($flightBookingDetails->status == 2)
                    <span style="color: green; font-weight: 600">Ticket Issued</span>
                @endif
                @if ($flightBookingDetails->status == 3)
                    <span style="color: red; font-weight: 600">Booking Cancelled</span>
                @endif
                @if ($flightBookingDetails->status == 4)
                    <span style="color: red; font-weight: 600">Ticket Cancelled</span>
                @endif
            </td>
        </tr>
    </table>


    <table border="0" style="width: 100%; padding: 0; margin: 0; margin-top: 15px; border-collapse: collapse;">

        <tr>
            <th colspan="4" style="text-align: center; font-size: 14px; background-color: lightblue; padding: 4px 0px; border-radius: 4px">Flight Itinery</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left; font-size: 14px; padding: 4px 0px;">{{ $flightBookingDetails->departure_location }} - {{ $flightBookingDetails->arrival_location }}</th>
        </tr>

        @foreach ($flightSegments as $index => $segment)
            <tr>
                <td style="width: 30%; font-size: 14px; padding: 5px 10px 5px 0; border-right: 1px solid gray; text-align: left;">

                    <table border="0" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td>
                                <img src="{{ public_path('airlines_logo/'.strtolower($segment->carrier_operating_code).'.png') }}" alt="Airline Logo" style="max-height: 50px; display: block;">
                            </td>
                            <td>
                                @php
                                    $airlineInfo = DB::table('airlines')
                                        ->where('iata', $segment->carrier_operating_code)
                                        ->where('active', 'Y')
                                        ->first();
                                @endphp
                                <p style="margin: 0; margin-bottom: 2px;">{{ $airlineInfo ? $airlineInfo->name : 'N/A' }}</p>
                                <p style="margin: 0; margin-bottom: 2px;">{{ $segment->carrier_operating_code }}-{{ $segment->carrier_operating_flight_number }}</p>
                                <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Aircraft: {{ getAircraftName($segment->carrier_equipment_code) }}</p>
                                <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ getCabinClass($segment->cabin_code) }} ({{$segment->booking_code}}) Class</p>
                            </td>
                        </tr>
                    </table>

                </td>
                <td style="width: 25%; font-size: 14px; padding: 5px 10px; border-right: 1px solid gray;">
                    @php
                        $departureLocation = DB::table('city_airports')->where('airport_code', $segment->departure_airport_code)->first();
                        $departure = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                        $departureDateTime = explode('T', $departure);
                    @endphp
                    <p style="margin: 0; margin-bottom: 2px;">{{ $departureLocation->city_name }} ({{ $departureLocation->city_code }})</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ $departureLocation->airport_name }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Terminal - {{ $segment->departure_terminal }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{substr($departureDateTime[1], 0, 5)}}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{date('D, j M Y', strtotime($departureDateTime[0]))}}</p>
                </td>
                <td style="width: 25%; font-size: 14px; padding: 5px 10px; border-right: 1px solid gray;">
                    @php
                        $arrivalLocation = DB::table('city_airports')->where('airport_code', $segment->arrival_airport_code)->first();
                        $arrival = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['ArrivalDateTime'] : null;
                        $arrivalDateTime = explode('T', $arrival);
                    @endphp
                    <p style="margin: 0; margin-bottom: 2px;">{{ $arrivalLocation->city_name }} ({{ $arrivalLocation->city_code }})</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ $arrivalLocation->airport_name }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Terminal - {{ $segment->arrival_terminal }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{substr($arrivalDateTime[1], 0, 5)}}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{date('D, j M Y', strtotime($arrivalDateTime[0]))}}</p>
                </td>
                <td style="width: 20%; font-size: 14px; padding: 5px 10px;">
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">
                        Airlines PNR:
                        @php
                            if($flightBookingDetails->airlines_pnr) {
                                $pnrList = explode(',', $flightBookingDetails->airlines_pnr);
                                $airlinePNR = isset($pnrList[$index]) ? $pnrList[$index] : 'N/A';
                            }
                            echo $airlinePNR;
                        @endphp
                    </p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Baggage: @if($segment->baggage_allowance) {{ $segment->baggage_allowance }} @else N/A @endif</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Duration: {{ $segment->elapsed_time }} mins</p>
                </td>
            </tr>

            @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code == $flightBookingDetails->arrival_location)
            <tr>
                <th colspan="4" style="text-align: left; font-size: 14px; padding: 4px 0px;">{{ $flightBookingDetails->arrival_location }} - {{ $flightBookingDetails->departure_location }}</th>
            </tr>
            @endif

            @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code != $flightBookingDetails->arrival_location)
            <tr>
                <td colspan="4" style="background: lightgoldenrodyellow; text-align: center; font-size: 14px; padding: 4px 0px; color: brown;">
                    @php
                        $firstArrival = new DateTime($arrivalDateTime[0].''.$arrivalDateTime[1]);
                        $secondDepartureRes = $bookingResSegs ? $bookingResSegs[$index+1]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                        $secondDepartureDateTime = explode('T', $secondDepartureRes);
                        $secondDeparture = new DateTime($secondDepartureDateTime[0].''.$secondDepartureDateTime[1]);
                        $interval = $firstArrival->diff($secondDeparture);
                        echo $interval->h . " hrs " . $interval->i . " mins Transit in " . $arrivalLocation->city_name . " (" . $arrivalLocation->airport_code . ")";
                    @endphp
                </td>
            </tr>
            @endif

        @endforeach

    </table>

    <h5 style="margin: 20px 0px 6px 0px; padding: px; font-size: 12px;">Travel Note:</h5>
    <ul style="padding: 0px; margin: 0px; padding-left: 15px">
        <li style="font-size: 12px; margin-bottom: 2px;">Check in counter will open before 1.30 hours of domestic and 3 hours of international flight departure.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Passenger reporting late for check in may be refused to board on flight. Please bring a valid photo ID.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Check in counter will be closed before 30 minutes of domestic and 60 minutes of international flight departure.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Boarding gate will be closed before 20 minutes of domestic and 30 minutes of international flight departure.</li>
    </ul>

</body>
</html>
