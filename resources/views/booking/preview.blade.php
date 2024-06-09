@extends('master')

@section('header_css')
    <link href="{{ url('assets') }}/admin-assets/css/pnr_tickekt_copy.css" rel="stylesheet" />
    <style>
        @media print {
            .btn_container{
                display: none;
            }
            img.rounded-circle{
                display: none;
            }
            .navbar-icon{
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="pnr_copy_container">
        <div class="btn_container">
            <button class="btn_print" onclick="window.print()">
                <i class="flaticon2-printer"></i>Print PDF
            </button>
        </div>
        <div class="invoice-wrap">
            <div class="i-invoice-logo-div">
                <div>
                    @php
                        $companyProfile = App\Models\CompanyProfile::where('user_id', Auth::user()->id)->first();
                    @endphp

                    @if ($companyProfile && $companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                        <img class="invoice-logo" src="{{ url($companyProfile->logo) }}" style="height: 50px;" />
                    @else
                        <img class="invoice-logo" src="{{ url('assets') }}/img/logo.svg" style="height: 50px;" />
                    @endif

                </div>
            </div>
            <h4 class="flight-h4">
                <span class="i-from">
                    Departure Date: {{ date('d M Y', strtotime($flightBookingDetails->departure_date)) }}
                </span>
                <span class="i-from">
                    ({{ $flightBookingDetails->departure_location }}
                    <small class="i-from-date"><i class="fa fa-caret-right fs-19"></i></small>
                    {{ $flightBookingDetails->arrival_location }})
                </span>
            </h4>
            <div class="i-prepare-container">
                <div class="i-prepare-child">
                    <div class="prepare">PREPARED FOR</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{ $flightBookingDetails->traveller_name }}</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{ $flightBookingDetails->traveller_email }}</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{ $flightBookingDetails->traveller_contact }}</div>
                </div>
                <div class="i-company-name">{{ $companyProfile->name }}</div>
            </div>
            <div class="i-code-div">
                <div class="i-air-code">
                    <div class="i-gds-lh">GDS CODE: {{ $flightBookingDetails->gds }}</div>
                    <div>PNR CODE: {{ $flightBookingDetails->pnr_id }}</div>
                </div>
                <div class="i-air-code">
                    <div class="i-company-code">
                        Booking CODE : {{ $flightBookingDetails->booking_no }}
                    </div>
                </div>
            </div>
            <div class="flight-info-container">
                <div class="flight-info-left">
                    <div class="d-flex align-items-start">
                        <span class="flight-departure">DEPARTURE TIME:
                            <strong>
                                {{ $flightSegments[0]->departure_time }}
                            </strong>
                        </span>
                    </div>
                </div>
                <div class="flight-info-right">
                    <div class="flight-notice">
                        Please verify flight times prior to departure
                    </div>
                </div>
            </div>
            <div class="i-ticket-container">
                <div class="airline-info">
                    <div class="airline-name mb-2">
                        Governing Carriers:<br>
                        @php
                            $governingCarriersArray = array_unique(explode(" ",$flightBookingDetails->governing_carriers));
                            foreach ($governingCarriersArray as $governingCarrier) {
                               echo DB::table('airlines')->where('iata', $governingCarrier)->where('active', 'Y')->first()->name."<br>";
                            }
                        @endphp
                    </div>
                    {{-- <div class="flight-details">{{ $flightSegments[0]->carrier_operating_code }}
                        {{ $flightSegments[0]->carrier_operating_flight_number }}</div> --}}
                    <div class="duration-label">Duration:</div>
                    <div class="flight-duration">
                        @php
                            $totalHours = 0;
                            $totalElapsedTime = 0;
                            $totalMilesFlown = 0;
                            foreach ($flightSegments as $flightSegment) {
                                $totalElapsedTime = $totalElapsedTime + $flightSegment->elapsed_time;
                                $totalMilesFlown = $totalMilesFlown + $flightSegment->total_miles_flown;
                            }
                            $totalHours = $totalElapsedTime / 60;
                        @endphp

                        {{ round($totalHours, 2) }} Hour
                    </div>
                    <div class="cabin-label">Cabin :</div>
                    <div class="cabin-class">{{ $flightSegments[0]->cabin_code }}</div>
                    <div class="status-label">Status :</div>
                    <div class="flight-status">
                        @if ($flightBookingDetails->status == 0)
                            Booking Requested
                        @endif
                        @if ($flightBookingDetails->status == 1)
                            Booking Done
                        @endif
                        @if ($flightBookingDetails->status == 2)
                            Ticket Issued
                        @endif
                        @if ($flightBookingDetails->status == 3)
                            Booking Cancelled
                        @endif
                        @if ($flightBookingDetails->status == 4)
                            Ticket Cancelled
                        @endif
                    </div>
                </div>
                <div class="flight-details-container">
                    <div class="route-details">
                        <div class="departure-info">
                            <h4>{{ $flightBookingDetails->departure_location }}</h4>
                            <div class="airport-info">
                                {{ DB::table('city_airports')->where('airport_code', $flightSegments[0]->departure_airport_code)->first()->city_name }}
                            </div>
                        </div>
                        <div class="arrival-info">
                            <h4><i class="fa fa-caret-right"></i> {{ $flightBookingDetails->arrival_location }}</h4>
                            <div class="airport-info">
                                {{ DB::table('city_airports')->where('airport_code', $flightSegments[count($flightSegments) - 1]->arrival_airport_code)->first()->city_name }}
                            </div>
                        </div>
                    </div>
                    <div class="timing-info">
                        <div class="time-info">
                            Departing At : <br />
                            <span class="time-label" style="text-transform: uppercase;">
                                {{ date('l, d M Y', strtotime($flightBookingDetails->departure_date)) }}
                            </span><br />
                            <strong>{{ $flightSegments[0]->departure_time }}</strong><br>
                            <span class="time-label"> (Local Time)</span>
                        </div>
                        <div class="time-info">
                            Arrival At :<br />
                            <span
                                class="time-label">{{ date('l, d M Y', strtotime($flightBookingDetails->departure_date)) }}</span><br />
                            <strong>{{ $flightSegments[count($flightSegments) - 1]->arrival_time }}</strong><br>
                            <span class="time-label">(Local Time)</span>
                        </div>
                    </div>
                </div>
                <div class="details-info">
                    <div>
                        Aircraft :<br />
                        {{ $flightSegments[0]->carrier_marketing_code }}-{{ $flightSegments[0]->carrier_marketing_flight_number }}
                    </div>
                    <div>
                        Distance (Miles) :<br />
                        {{ $totalMilesFlown }}
                    </div>
                    {{-- <div>Meals: {{$flightSegments[0]->carrier_marketing_code}}</div> --}}
                </div>
            </div>
            <div class="baggage-info-container">
                <div class="baggage-details">
                    <div>
                        Cabin Baggage: {{ $flightSegments[0]->baggage_allowance }}
                    </div>
                </div>
            </div>
            <div class="passenger-info-container">
                <div class="passenger-column">
                    <div class="passenger-title">Passenger Name</div>
                    @foreach ($flightPassangers as $flightPassanger)
                        <div class="passenger-details">
                            <i class="fa fa-angle-double-right"></i>
                            {{ $flightPassanger->first_name }} {{ $flightPassanger->last_name }}
                        </div>
                    @endforeach
                </div>
                <div class="seat-column">
                    <div class="seat-title">Type :</div>
                    @foreach ($flightPassangers as $flightPassanger)
                        <div class="seat-details">{{ $flightPassanger->passanger_type }}</div>
                    @endforeach
                </div>
            </div>
            <hr />
        </div>
    </div>
@endsection
