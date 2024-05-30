@extends('master')

@section('header_css')
    <link href="{{ url('assets') }}/admin-assets/css/pnr_tickekt_copy.css" rel="stylesheet" />
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

                    @if($companyProfile && $companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                        <img class="invoice-logo" src="{{url($companyProfile->logo)}}" />
                    @else
                        <img class="invoice-logo" src="{{ url('assets') }}/img/logo.svg" />
                    @endif

                </div>
            </div>
            <h4 class="flight-h4">
                <span class="i-from">
                    Departure Date: {{date("d M Y", strtotime($flightBookingDetails->departure_date))}}
                </span>
                <span class="i-from">
                    ({{$flightBookingDetails->departure_location}}
                    <small class="i-from-date"><i class="fa fa-caret-right fs-19"></i></small>
                    {{$flightBookingDetails->arrival_location}})
                </span>
            </h4>
            <div class="i-prepare-container">
                <div class="i-prepare-child">
                    <div class="prepare">PREPARED FOR</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{$flightBookingDetails->traveller_name}}</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{$flightBookingDetails->traveller_email}}</div>
                    <div class="prepare-p-name" style="font-size: 16px">{{$flightBookingDetails->traveller_contact}}</div>
                </div>
                <div class="i-company-name">{{$companyProfile->name}}</div>
            </div>
            <div class="i-code-div">
                <div class="i-air-code">
                    <div class="i-gds-lh">GDS CODE: {{$flightBookingDetails->gds}}</div>
                    <div>PNR CODE: {{$flightBookingDetails->pnr_id}}</div>
                </div>
                <div class="i-air-code">
                    <div class="i-company-code">
                        Booking CODE : {{$flightBookingDetails->booking_no}}
                    </div>
                </div>
            </div>
            <div class="flight-info-container">
                <div class="flight-info-left">
                    <div class="d-flex align-items-start">
                        <span class="flight-departure">DEPARTURE TIME:
                            <strong>
                                MONDAY, 04 MAR 2024
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
                    <div class="airline-name">Biman Bangladesh Airlines</div>
                    <div class="flight-details">BG 347</div>
                    <div class="duration-label">Duration:</div>
                    <div class="flight-duration">345hr</div>
                    <div class="cabin-label">Cabin :</div>
                    <div class="cabin-class">Y</div>
                    <div class="status-label">Status :</div>
                    <div class="flight-status"></div>
                </div>
                <div class="flight-details-container">
                    <div class="route-details">
                        <div class="departure-info">
                            <h4>DAC</h4>
                            <div class="airport-info">Dhaka, Bangladesh (DAC)</div>
                        </div>
                        <div class="arrival-info">
                            <h4><i class="fa fa-caret-right"></i> DXB</h4>
                            <div class="airport-info">
                                Dubai, United Arab Emirates (DXB)
                            </div>
                        </div>
                    </div>
                    <div class="timing-info">
                        <div class="time-info">
                            Departing At : <br />
                            <span class="time-label">SUNDAY, 25 FEB 2024</span><br />
                            <strong>08:35 PM</strong>
                            <span class="time-label"> (Local Time)</span>
                        </div>
                        <div class="time-info">
                            Arrival At :<br />
                            <span class="time-label">MONDAY, 26 FEB 2024</span><br />
                            <strong>12:20 AM</strong>
                            <span class="time-label">(Local Time)</span>
                        </div>
                    </div>
                </div>
                <div class="details-info">
                    <div>
                        Aircraft :<br />
                        Boing-372
                    </div>
                    <div>
                        Distance (Miles) :<br />
                        2202
                    </div>
                    <div>Meals:</div>
                </div>
            </div>
            <div class="baggage-info-container">
                <div class="baggage-details">
                    <div>
                        Checked Baggage (Kg): Adult,; Checked Baggage: Adult, Please see
                        the airline rules
                    </div>
                    <div>
                        Cabin Baggage (Pcs): Adult,1; Checked Baggage: Adult, Please see
                        the airline rules
                    </div>
                </div>
            </div>
            <div class="passenger-info-container">
                <div class="passenger-column">
                    <div class="passenger-title">Passenger Name</div>
                    <div class="passenger-details">
                        <i class="fa fa-angle-double-right"></i>
                        jjjj/ uuuu
                    </div>
                </div>
                <div class="seat-column">
                    <div class="seat-title">Seats :</div>
                    <div class="seat-details">Check-in Required</div>
                </div>
            </div>
            <hr />
        </div>
    </div>
@endsection
