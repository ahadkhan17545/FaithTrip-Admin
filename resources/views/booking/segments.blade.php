<div class="row mb-2">
    <div class="col-lg-12">
        <table class="table table-bordered border-dark table-sm">
            <thead>
                <tr class="table-success">
                    <th scope="col" class="text-center" colspan="9" style="font-size: 14px">Flight Itinerary</th>
                </tr>
                <tr class="table-success">
                    <th scope="col" class="text-center">Sl</th>
                    <th scope="col" class="text-center">Airline & Flight Info</th>
                    <th scope="col" class="text-center">Class</th>
                    <th scope="col" class="text-center">Departure From</th>
                    <th scope="col" class="text-center">Departure Datetime</th>
                    <th scope="col" class="text-center">Destination</th>
                    <th scope="col" class="text-center">Arrival Datetime</th>
                    <th scope="col" class="text-center">Baggage</th>
                    <th scope="col" class="text-center">Duration</th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-warning">
                    <td class="text-center align-middle" colspan="9" style="font-size: 14px">
                        {{ $flightBookingDetails->departure_location }} - {{ $flightBookingDetails->arrival_location }}
                    </td>
                </tr>
                @foreach ($flightSegments as $index => $segment)
                    <tr>
                        <th class="text-center align-middle" scope="row">{{ $index + 1 }}</th>
                        <td class="text-center align-middle">
                            <div style="display: inline-flex; align-items: center;">
                                <img style="display: block; max-height: 50px;" alt="{{$segment->carrier_operating_code}}" src="{{ url('airlines_logo') }}/{{ strtolower($segment->carrier_operating_code) }}.png" loading="lazy">
                                <div style="padding-left: 10px; text-align: left;">
                                    @php
                                        $airlineInfo = DB::table('airlines')
                                            ->where('iata', $segment->carrier_operating_code)
                                            ->where('active', 'Y')
                                            ->first();
                                    @endphp
                                    <p style="margin: 0;">{{ $airlineInfo ? $airlineInfo->name : 'N/A' }}</p>
                                    <p style="margin: 0;">{{ $segment->carrier_operating_code }}-{{ $segment->carrier_operating_flight_number }}</p>
                                    <p style="margin: 0;">Aircraft : {{ getAircraftName($segment->carrier_equipment_code) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle">{{ getCabinClass($segment->cabin_code) }} ({{$segment->booking_code}})</td>
                        <td class="text-center align-middle">
                            @php
                                $departureLocation = DB::table('city_airports')->where('airport_code', $segment->departure_airport_code)->first();
                            @endphp
                            {{ $departureLocation->city_name }} ({{ $departureLocation->city_code }})
                            <p style="margin: 0;">{{ $departureLocation->airport_name }}</p>
                            <p style="margin: 0;">Terminal : {{ $segment->departure_terminal }}</p>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $departure = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                            @endphp
                            @if($departure)
                                @php
                                    $departureDateTime = explode('T', $departure);
                                @endphp
                                <p style="margin: 0;">{{substr($departureDateTime[1], 0, 5)}}</p>
                                <p style="margin: 0;">{{date('D, j M Y', strtotime($departureDateTime[0]))}}</p>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $arrivalLocation = DB::table('city_airports')->where('airport_code', $segment->arrival_airport_code)->first();
                            @endphp
                            {{ $arrivalLocation->city_name }} ({{ $arrivalLocation->city_code }})
                            <p style="margin: 0;">{{ $arrivalLocation->airport_name }}</p>
                            <p style="margin: 0;">Terminal : {{ $segment->arrival_terminal }}</p>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $arrival = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['ArrivalDateTime'] : null;
                            @endphp
                            @if($arrival)
                                @php
                                    $arrivalDateTime = explode('T', $arrival);
                                @endphp
                                <p style="margin: 0;">{{substr($arrivalDateTime[1], 0, 5)}}</p>
                                <p style="margin: 0;">{{date('D, j M Y', strtotime($arrivalDateTime[0]))}}</p>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($segment->baggage_allowance)
                            {{ $segment->baggage_allowance }}
                            @else
                            <span class="text-danger">Not Specified</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $segment->elapsed_time }} mins</td>
                    </tr>

                    @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code == $flightBookingDetails->arrival_location)
                    <tr class="table-warning">
                        <td class="text-center align-middle" colspan="9" style="font-size: 14px">
                            {{ $flightBookingDetails->arrival_location }} - {{ $flightBookingDetails->departure_location }}
                        </td>
                    </tr>
                    @endif

                    @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code != $flightBookingDetails->arrival_location)
                    <tr class="table-active">
                        <td class="text-center align-middle" colspan="9" style="font-size: 14px">
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
            </tbody>
        </table>
    </div>
</div>

