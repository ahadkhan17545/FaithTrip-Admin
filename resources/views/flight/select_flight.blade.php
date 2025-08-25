@extends('master')

@section('header_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container--default .select2-selection--single .select2-selection__arrow b::before{
            content: "" !important;
        }
        .select2{
            border: 1px solid lightgray !important;
            border-radius: 4px !important;
            padding: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            top: 4px !important;
            right: 4px !important;
        }

        /* live search css start */
        ul.live_search_box {
            position: absolute;
            top: 98%;
            left: 0px;
            z-index: 999;
            background: white;
            border: 1px solid lightgray;
            width: 100%;
            padding: 0px;
            border-radius: 0px 0px 4px 4px;
        }

        ul.live_search_box li.live_search_item {
            list-style: none;
            border-bottom: 1px solid lightgray;
        }

        ul.live_search_box li.live_search_item:last-child {
            border-bottom: none;
        }

        ul.live_search_box li.live_search_item a.live_search_product_link {
            display: flex;
            padding: 8px 12px;
            transition: all .1s linear;
        }

        ul.live_search_box li.live_search_item a.live_search_product_link:hover {
            box-shadow: 1px 1px 5px #cecece inset;
        }

        ul.live_search_box li.live_search_item a.live_search_product_link h6.live_search_product_title {
            margin-bottom: 0px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #1e1e1e;
            font-size: 14px;
        }

        /* live search css end */
    </style>
@endsection

@section('content')

    @php
        $countries = DB::table('country')->orderBy('name', 'asc')->get();
    @endphp

    <div class="row">
        <div class="col-xl-8 mainContent">
            <div class="theiaStickySidebar">

                @php

                    // echo "<pre>";
                    // print_r($revlidatedData);
                    // echo "</pre>";

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
                @endphp

                <div class="card shadow border-0 mb-3">
                    <div class="content-header media">
                        <div class="media-body">
                            <h3 class="content-header_title fs-23 mb-0">Flight Details</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">

                            <tr>
                                <th colspan="2" style="text-align:center">
                                    @if (count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions']) == 1)
                                        One-Way
                                    @else
                                        Round-Trip
                                    @endif
                                </th>
                            </tr>

                            <tr>
                                <th style="width: 25%">Departure Datetime</th>
                                <td>
                                    @php
                                        $dateString = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'].' '.$segmentArray[0]['departure']['time'];
                                        $date = new DateTime($dateString);
                                        $formattedDate = $date->format('jS F, Y g:i a');
                                        echo $formattedDate;
                                    @endphp
                                </td>
                            </tr>

                            <tr>
                                <th style="width: 25%">Departure Airport</th>
                                <td>
                                    @php
                                        $beginAirportCode = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['beginAirport'];
                                        $beginAirportInfo = DB::table('city_airports')
                                                            ->where('airport_code', $beginAirportCode)
                                                            ->first();
                                        if ($beginAirportInfo) {
                                            echo $beginAirportInfo->airport_name . ', ' . $beginAirportInfo->city_name . ', ' . $beginAirportInfo->country_name .' ('.$beginAirportInfo->airport_code.')';
                                        }
                                    @endphp
                                </td>
                            </tr>

                            <tr>
                                <th style="width: 25%">Arrival Airport</th>
                                <td>
                                    @php
                                        $endAirportCode = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['endAirport'];
                                        $endAirportInfo = DB::table('city_airports')
                                                                ->where('airport_code', $endAirportCode)
                                                                ->first();
                                        if ($endAirportInfo) {
                                            echo $endAirportInfo->airport_name . ', ' . $endAirportInfo->city_name . ', ' . $endAirportInfo->country_name .' ('.$endAirportInfo->airport_code.')';
                                        }
                                    @endphp
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>

                <div class="card shadow border-0 mb-3">
                    <div class="card-body">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Flight Segments
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">

                                        @foreach ($segmentArray as $segmentIndex => $segmentData)
                                            <div class="flight-info border rounded mb-2">
                                                <div class="flight-scroll review-article">
                                                    <div
                                                        class="align-items-center d-flex custom-gap justify-content-between w-100">
                                                        <div class="align-items-center d-flex gap-4 text-center">
                                                            <div class="brand-img">
                                                                <img src="{{ url('airlines_logo') }}/{{ strtolower($segmentData['carrier']['operating']) }}.png">
                                                            </div>
                                                            <div class="airline-box">
                                                                <div class="font-weight-600 fs-13">
                                                                    {{ $segmentData['carrier']['operating'] }}
                                                                </div>
                                                                <div class="font-weight-600 fs-13 text-muted w-max-content">
                                                                    {{ $segmentData['carrier']['operatingFlightNumber'] }}
                                                                    -
                                                                    {{ $segmentData['carrier']['equipment']['code'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="font-weight-600 fs-13">
                                                                {{ $segmentData['departure']['airport'] }}
                                                            </div>
                                                            <span class="fs-12 font-weight-600"
                                                                style="width: 80px; display: inline-block;">
                                                                @php
                                                                    $departureDateTime = new DateTime(
                                                                        $segmentData['departure']['time'],
                                                                    );
                                                                    echo $departureDateTime->format('h:i a');
                                                                @endphp
                                                            </span><br>
                                                            <span class="text-muted fs-12">
                                                                Terminal -
                                                                {{ isset($segmentData['departure']['terminal']) ? $segmentData['departure']['terminal'] : 'N/A' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="font-weight-600 fs-13">
                                                                {{ $segmentData['arrival']['airport'] }}
                                                            </div>
                                                            <span class="fs-12 font-weight-600"
                                                                style="width: 80px; display: inline-block;">
                                                                @php
                                                                    $arrivalDateTime = new DateTime(
                                                                        $segmentData['arrival']['time'],
                                                                    );
                                                                    echo $arrivalDateTime->format('h:i a');
                                                                @endphp
                                                            </span><br>
                                                            <span class="text-muted fs-12">
                                                                Terminal -
                                                                {{ isset($segmentData['arrival']['terminal']) ? $segmentData['arrival']['terminal'] : 'N/A' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-center fs-14 w-100">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <span
                                                                    class="d-inline-flex align-items-center w-max-content">
                                                                    {{ App\Models\CustomFunction::convertMinToHrMin($segmentData['elapsedTime']) }}
                                                                </span>
                                                                <span
                                                                    class="d-inline-flex align-items-center w-max-content">&nbsp;<span
                                                                        class="text-muted">|</span>&nbsp;
                                                                    {{ isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode']) ? 'Meal - ' . $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode'] : 'N/A' }}
                                                                </span>
                                                                <span
                                                                    class="d-inline-flex align-items-center w-max-content">&nbsp;<span
                                                                        class="text-muted">|</span>&nbsp;
                                                                    {{ isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode']) ? 'Booking Code - ' . $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'] : 'N/A' }}
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="two-dots my-3 text-muted position-relative border-top">
                                                                <span class="flight-service">
                                                                    <span
                                                                        class="type-text px-2 position-relative">Flight</span>
                                                                </span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <span
                                                                    class="d-inline-flex align-items-center w-max-content">
                                                                    @php
                                                                        $passangerWisebaggage =
                                                                            $revlidatedData['groupedItineraryResponse'][
                                                                                'itineraryGroups'
                                                                            ][0]['itineraries'][0][
                                                                                'pricingInformation'
                                                                            ][0]['fare']['passengerInfoList'];

                                                                        foreach (
                                                                            $passangerWisebaggage
                                                                            as $passangerWisebaggageInfo
                                                                        ) {
                                                                            if (
                                                                                isset(
                                                                                    $passangerWisebaggageInfo[
                                                                                        'passengerInfo'
                                                                                    ]['baggageInformation'][0][
                                                                                        'allowance'
                                                                                    ]['ref'],
                                                                                )
                                                                            ) {
                                                                                $baggageRef =
                                                                                    $passangerWisebaggageInfo[
                                                                                        'passengerInfo'
                                                                                    ]['baggageInformation'][0][
                                                                                        'allowance'
                                                                                    ]['ref'];
                                                                                if (
                                                                                    isset(
                                                                                        $revlidatedData[
                                                                                            'groupedItineraryResponse'
                                                                                        ]['baggageAllowanceDescs'][
                                                                                            $baggageRef - 1
                                                                                        ],
                                                                                    )
                                                                                ) {
                                                                                    echo $passangerWisebaggageInfo[
                                                                                        'passengerInfo'
                                                                                    ]['passengerType'] .
                                                                                        '(' .
                                                                                        $passangerWisebaggageInfo[
                                                                                            'passengerInfo'
                                                                                        ]['passengerNumber'] .
                                                                                        '): ';

                                                                                    if (
                                                                                        isset(
                                                                                            $revlidatedData[
                                                                                                'groupedItineraryResponse'
                                                                                            ]['baggageAllowanceDescs'][
                                                                                                $baggageRef - 1
                                                                                            ]['pieceCount'],
                                                                                        )
                                                                                    ) {
                                                                                        echo 'Piece Count: ' .
                                                                                            $revlidatedData[
                                                                                                'groupedItineraryResponse'
                                                                                            ]['baggageAllowanceDescs'][
                                                                                                $baggageRef - 1
                                                                                            ]['pieceCount'] *
                                                                                                $passangerWisebaggageInfo[
                                                                                                    'passengerInfo'
                                                                                                ]['passengerNumber'];
                                                                                    }
                                                                                    if (
                                                                                        isset(
                                                                                            $revlidatedData[
                                                                                                'groupedItineraryResponse'
                                                                                            ]['baggageAllowanceDescs'][
                                                                                                $baggageRef - 1
                                                                                            ]['weight'],
                                                                                        )
                                                                                    ) {
                                                                                        echo $revlidatedData[
                                                                                            'groupedItineraryResponse'
                                                                                        ]['baggageAllowanceDescs'][
                                                                                            $baggageRef - 1
                                                                                        ]['weight'] *
                                                                                            $passangerWisebaggageInfo[
                                                                                                'passengerInfo'
                                                                                            ]['passengerNumber'];
                                                                                    }
                                                                                    if (
                                                                                        isset(
                                                                                            $revlidatedData[
                                                                                                'groupedItineraryResponse'
                                                                                            ]['baggageAllowanceDescs'][
                                                                                                $baggageRef - 1
                                                                                            ]['unit'],
                                                                                        )
                                                                                    ) {
                                                                                        echo ' ' .
                                                                                            $revlidatedData[
                                                                                                'groupedItineraryResponse'
                                                                                            ]['baggageAllowanceDescs'][
                                                                                                $baggageRef - 1
                                                                                            ]['unit'];
                                                                                    }

                                                                                    echo '&nbsp;&nbsp;';
                                                                                }
                                                                            }
                                                                        }

                                                                    @endphp
                                                                    &nbsp;
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if (isset($segmentArray[$segmentIndex + 1]) &&
                                                    isset($segmentData['arrival']['time']) &&
                                                    $segmentArray[$segmentIndex + 1]['departure']['time']
                                            )
                                                <div class="d-flex justify-center px-3">
                                                    <span class="fs-12 layover text-center">
                                                        @php
                                                            $time1 = substr($segmentData['arrival']['time'], 0, 8);
                                                            $time2 = substr(
                                                                $segmentArray[$segmentIndex + 1]['departure']['time'],
                                                                0,
                                                                8,
                                                            );
                                                            $time1Obj = DateTime::createFromFormat('H:i:s', $time1);
                                                            $time2Obj = DateTime::createFromFormat('H:i:s', $time2);
                                                            $interval = $time1Obj->diff($time2Obj);
                                                            $formattedDifference = sprintf(
                                                                '%dhr %dmin',
                                                                $interval->h + $interval->days * 24, // Total hours, including days if any
                                                                $interval->i, // Minutes
                                                            );
                                                            echo $formattedDifference . ' Layover';
                                                        @endphp
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- pricing info start --}}
                <div class="card shadow border-0 mb-3 d-xl-none">
                    @include('flight.pricing_info')
                </div>
                {{-- pricing info end --}}




                <form id="submit_ticket_reservation_info" action="{{ url('create/pnr/with/booking/sabre') }}" method="POST"
                    class="on-submit">
                    @csrf

                    @php
                        session(['revlidatedData' => $revlidatedData]);
                    @endphp

                    <input type="hidden" name="gds" value="Sabre">
                    <input type="hidden" name="departure_date"
                        value="{{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'] }}">
                    <input type="hidden" name="departure_location"
                        value="{{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureLocation'] }}">
                    @php
                        $legDescriptionsLastIndex =
                            count(
                                $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription'][
                                    'legDescriptions'
                                ],
                            ) - 1;
                    @endphp
                    <input type="hidden" name="arrival_location"
                        value="{{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][$legDescriptionsLastIndex]['arrivalLocation'] }}">
                    <input type="hidden" name="governing_carriers"
                        value="{{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['governingCarriers'] }}">
                    <input type="hidden" name="currency"
                        value="{{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }}">

                    <div class="card shadow border-0 mb-3">
                        <div class="content-header media">
                            <div class="media-body">
                                <h3 class="content-header_title fs-23 mb-0">Traveler Details</h3>
                                <p class="mb-0 text-danger">Traveler Details will be used as primary contact</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    <label for="Email" style="line-height: 35px">Traveler Name</label>
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3">
                                    <input name="traveller_name" id="traveller_name" type="text" class="form-control" placeholder="Full Name" required="">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    <label for="traveller_email" style="line-height: 35px">Traveler Email</label>
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3">
                                    <input name="traveller_email" id="traveller_email" type="email" class="form-control" placeholder="user@email.com" required="">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    <label style="line-height: 35px">Contact Number</label>
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3" style="position: relative">
                                    <input name="traveller_contact" id="search_keyword" onkeyup="liveSearchPassanger()" autocomplete="off" type="text" class="form-control" placeholder="+8801*********" required="">
                                    <ul class="live_search_box d-none"></ul>
                                </div>
                            </div>

                            @php $passangerTitleIndex=0; @endphp
                            @foreach ($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerInfoList)
                                @for ($i = 1; $i <= $passengerInfoList['passengerInfo']['passengerNumber']; $i++)
                                    <hr>
                                    <h6 class="fw-bold mb-4">Please fill the information for
                                        {{ $passengerInfoList['passengerInfo']['passengerType'] }} - {{ $i }}
                                    </h6>

                                    @php
                                        $savedPassangers = DB::table('saved_passangers')
                                                            ->where('type', $passengerInfoList['passengerInfo']['passengerType'])
                                                            ->where('saved_by', Auth::user()->id)
                                                            ->orderBy('first_name', 'asc')
                                                            ->get();
                                    @endphp

                                    <div class="form-row mt-3">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px; font-weight: 600">Select From List</label>
                                            <span class="text-danger"></span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="input-select position-relative">
                                                <select id="select_passenger_{{$passangerTitleIndex}}" class="mySelect2" onchange="fillUpCustomerDetails(this, {{$passangerTitleIndex}})">
                                                    <option value="">Select Customer</option>
                                                    @foreach($savedPassangers as $savedPassanger)
                                                    <option

                                                        value="{{$savedPassanger->id}}"
                                                        data_title="{{$savedPassanger->title}}"
                                                        data_first_name="{{$savedPassanger->first_name}}"
                                                        data_last_name="{{$savedPassanger->last_name}}"
                                                        data_email="{{$savedPassanger->email}}"
                                                        data_contact="{{$savedPassanger->contact}}"
                                                        data_dob="{{$savedPassanger->dob}}"
                                                        data_age="{{$savedPassanger->age}}"
                                                        data_document_type="{{$savedPassanger->document_type}}"
                                                        data_document_no="{{$savedPassanger->document_no}}"
                                                        data_document_expire_date="{{$savedPassanger->document_expire_date}}"
                                                        data_document_issue_country="{{$savedPassanger->document_issue_country}}"
                                                        data_nationality="{{$savedPassanger->nationality}}"
                                                        data_frequent_flyer_no="{{$savedPassanger->frequent_flyer_no}}"

                                                    >{{$savedPassanger->title}} {{$savedPassanger->first_name}} {{$savedPassanger->last_name}} ({{$savedPassanger->dob}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="passanger_type[]" value="{{ $passengerInfoList['passengerInfo']['passengerType'] }}">
                                    <div class="form-row mt-3">
                                        <div class="col-sm-12 col-md-3 font-weight-500 text-left text-md-right pr-3 px-3">
                                            <label>Passenger Title </label>
                                            <span class="text-danger">*</span>
                                        </div>

                                        @if($passengerInfoList['passengerInfo']['passengerType'] == 'ADT')
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="titles[{{ $passangerTitleIndex }}]" id="passanger_title_{{ $passangerTitleIndex }}_mr" value="Mr.">
                                                <label class="form-check-label" for="passanger_title_{{ $passangerTitleIndex }}_mr">Mr.</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="titles[{{ $passangerTitleIndex }}]" id="passanger_title_{{ $passangerTitleIndex }}_mrs" value="Mrs.">
                                                <label class="form-check-label" for="passanger_title_{{ $passangerTitleIndex }}_mrs">Mrs.</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="titles[{{ $passangerTitleIndex }}]" id="passanger_title_{{ $passangerTitleIndex }}_ms" value="Ms.">
                                                <label class="form-check-label" for="passanger_title_{{ $passangerTitleIndex }}_ms">Ms.</label>
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="titles[{{ $passangerTitleIndex }}]" id="passanger_title_{{ $passangerTitleIndex }}_mstr" value="Mstr.">
                                                <label class="form-check-label" for="passanger_title_{{ $passangerTitleIndex }}_mstr">Mstr.</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="titles[{{ $passangerTitleIndex }}]" id="passanger_title_{{ $passangerTitleIndex }}_miss" value="Miss.">
                                                <label class="form-check-label" for="passanger_title_{{ $passangerTitleIndex }}_miss">Miss.</label>
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                    <div class="form-row mt-3">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Passenger Name</label>
                                            <span class="text-danger">*</span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="input-select position-relative">
                                                        <input name="first_name[]" id="first_name_{{$passangerTitleIndex}}" type="text" class="form-control" placeholder="First name" required="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="last_name[]" id="last_name_{{$passangerTitleIndex}}" type="text" class="form-control" placeholder="Last name" required="">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div
                                            class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Contact Info</label>
                                            <span class="text-danger"></span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="input-select position-relative">
                                                        <input name="email[]" id="email_{{$passangerTitleIndex}}" type="text" class="form-control" placeholder="example@email.com">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="phone[]" id="phone_{{$passangerTitleIndex}}" type="text" class="form-control" placeholder="Phone No.">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Date of birth</label>
                                            <span class="text-danger">*</span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col">
                                                    <input required="" id="dob_{{$passangerTitleIndex}}" class="form-control" type="date"
                                                    placeholder="dd-mm-yyyy" name="dob[]" min="1900-01-01"
                                                    max="{{ date('Y-m-d') }}">
                                                </div>

                                                @if($passengerInfoList['passengerInfo']['passengerType'] != 'ADT')
                                                <div class="col">
                                                    <input id="age_{{$passangerTitleIndex}}" class="form-control" type="number" name="age[]" required=""
                                                    @if($passengerInfoList['passengerInfo']['passengerType'] == 'CNN') placeholder="ex. 07 (in years)" max="11" min="2" @else  placeholder="ex. 15 (in months)" max="24" min="0" @endif>
                                                </div>
                                                @else
                                                    <input id="age_{{$passangerTitleIndex}}" class="form-control" type="hidden" value="18" name="age[]">
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Document Type</label>
                                            <span class="text-danger">*</span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <select name="document_type[]" id="document_type_{{$passangerTitleIndex}}" class="form-select" required>
                                                        <option value="1" selected="">Passport</option>
                                                        <option value="2">National id</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="document_no[]" id="document_no_{{$passangerTitleIndex}}" type="text" class="form-control"
                                                        placeholder="Document Number" required="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div
                                            class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Document expiration</label>
                                            <span class="text-danger">*</span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <input required="" id="document_expire_date_{{$passangerTitleIndex}}" name="document_expire_date[]" type="date"
                                                        class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <select id="document_issue_country_{{$passangerTitleIndex}}" required="" name="document_issue_country[]" class="form-select" aria-label="Default select example">
                                                        <option selected="" disabled="">Select Issue Country
                                                        </option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->iso }}">{{ $country->nicename }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                            <label style="line-height: 35px">Nationality</label>
                                            <span class="text-danger">*</span>
                                        </div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <select id="nationality_{{$passangerTitleIndex}}" required="" name="nationality[]" class="form-select"
                                                        aria-label="Default select example">
                                                        <option selected="" disabled="">Please Select Nationality
                                                        </option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->iso3 }}">{{ $country->nicename }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" id="frequent_flyer_no_{{$passangerTitleIndex}}" name="frequent_flyer_no[]" class="form-control"
                                                        placeholder="Frequent Flyer No.">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3"></div>
                                        <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                            <label class="d-block mt-2">
                                                <input type="checkbox" name="save_passanger[]" value="{{ $passangerTitleIndex }}"> Save Passenger Information
                                            </label>
                                        </div>
                                    </div>

                                    @php $passangerTitleIndex++; @endphp
                                @endfor
                            @endforeach

                            <hr>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Make booking</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
        <div class="col-xl-4 rightSidebar">
            {{-- pricing info start --}}
            <div class="theiaStickySidebar">
                <div class="card shadow border-0 mb-3 d-none d-xl-block">
                    @include('flight.pricing_info')
                </div>
            </div>
            {{-- pricing info end --}}
        </div>
    </div>
@endsection

@section('footer_js')
    <script>

        $(document).ready(function() {
            $('.mySelect2').select2();
        });

        function fillUpCustomerDetails(selectElement, id){

            var selectedValue = selectElement.value;
            var savetitle = selectElement.options[selectElement.selectedIndex].getAttribute('data_title');
            var saveFirstName = selectElement.options[selectElement.selectedIndex].getAttribute('data_first_name');
            var savedLastName = selectElement.options[selectElement.selectedIndex].getAttribute('data_last_name');
            var savedEmail = selectElement.options[selectElement.selectedIndex].getAttribute('data_email');
            var savedContact = selectElement.options[selectElement.selectedIndex].getAttribute('data_contact');
            var savedDob = selectElement.options[selectElement.selectedIndex].getAttribute('data_dob');
            var savedAge = selectElement.options[selectElement.selectedIndex].getAttribute('data_age');
            var savedDocumentType = selectElement.options[selectElement.selectedIndex].getAttribute('data_document_type');
            var savedDocumentNo = selectElement.options[selectElement.selectedIndex].getAttribute('data_document_no');
            var savedDocumentIssueCountry = selectElement.options[selectElement.selectedIndex].getAttribute('data_document_issue_country');
            var savedDocumentExpireDate = selectElement.options[selectElement.selectedIndex].getAttribute('data_document_expire_date');
            var savedNationality = selectElement.options[selectElement.selectedIndex].getAttribute('data_nationality');
            var savedFrequentFlyerNo = selectElement.options[selectElement.selectedIndex].getAttribute('data_frequent_flyer_no');

            if(savetitle == 'Mr.'){
                $("#passanger_title_"+id+"_mr").prop('checked', true);
            }
            if(savetitle == 'Mrs.'){
                $("#passanger_title_"+id+"_mrs").prop('checked', true);
            }
            if(savetitle == 'Ms.'){
                $("#passanger_title_"+id+"_ms").prop('checked', true);
            }
            if(savetitle == 'Mstr.'){
                $("#passanger_title_"+id+"_mstr").prop('checked', true);
            }
            if(savetitle == 'Miss.'){
                $("#passanger_title_"+id+"_miss").prop('checked', true);
            }

            $("#first_name_"+id).val(saveFirstName);
            $("#last_name_"+id).val(savedLastName);
            $("#email_"+id).val(savedEmail);
            $("#phone_"+id).val(savedContact);
            $("#dob_"+id).val(savedDob);
            $("#age_"+id).val(savedAge);
            $("#document_type_"+id).val(savedDocumentType);
            $("#document_no_"+id).val(savedDocumentNo);
            $("#document_expire_date_"+id).val(savedDocumentExpireDate);
            $("#document_issue_country_"+id).val(savedDocumentIssueCountry);
            $("#nationality_"+id).val(savedNationality);
            $("#frequent_flyer_no_"+id).val(savedFrequentFlyerNo);

            // alert(id);
        }

        function liveSearchPassanger(){
            var searchKeyword = $("#search_keyword").val();

            if(searchKeyword && searchKeyword != '' && searchKeyword != null){
                var formData = new FormData();
                formData.append("search_keyword", $("#search_keyword").val());

                $.ajax({
                    data: formData,
                    url: "{{ url('passanger/live/search') }}",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('.live_search_box').removeClass('d-none');
                        $('.live_search_box').html(data.searchResults);
                        // renderLazyImage();
                    },
                    error: function(data) {
                        toastr.options.positionClass = 'toast-bottom-right';
                        toastr.options.timeOut = 1000;
                        toastr.error("Something Went Wrong");
                    }
                });
            } else {
                $('.live_search_box').addClass('d-none');
            }
        }

        function autoFillUpForm(index){
            $('.live_search_box').addClass('d-none');
            var title = $("#passanger_title_"+index).val();
            var firstName = $("#passanger_first_name_"+index).val();
            var lastName = $("#passanger_last_name__"+index).val();
            var email = $("#passanger_email_"+index).val();
            var contact = $("#passanger_contact_"+index).val();
            var type = $("#passanger_type_"+index).val();
            var dob = $("#passanger_dob_"+index).val();
            var documentType = $("#passanger_document_type_"+index).val();
            var documentNo = $("#passanger_document_no_"+index).val();
            var documentExpireDate = $("#passanger_document_expire_date_"+index).val();
            var documentIssueCountry = $("#passanger_document_issue_country_"+index).val();
            var nationality = $("#passanger_nationality_"+index).val();
            var frequentFlyerNo = $("#passanger_frequent_flyer_no_"+index).val();

            if(title == 'Mr.'){
                $("#passanger_title_0_mr").prop('checked', true);
            }
            if(title == 'Mrs.'){
                $("#passanger_title_0_mrs").prop('checked', true);
            }
            if(title == 'Ms.'){
                $("#passanger_title_0_ms").prop('checked', true);
            }
            if(title == 'Mstr.'){
                $("#passanger_title_0_mstr").prop('checked', true);
            }
            if(title == 'Miss.'){
                $("#passanger_title_0_miss").prop('checked', true);
            }

            $("#traveller_name").val(title+" "+firstName+" "+lastName);
            $("#traveller_email").val(email);
            $("#search_keyword").val(contact);
            $("#first_name_0").val(firstName);
            $("#last_name_0").val(lastName);
            $("#email_0").val(email);
            $("#phone_0").val(contact);
            $("#dob_0").val(dob);
            $("#document_type_0").val(documentType);
            $("#document_no_0").val(documentNo);
            $("#document_expire_date_0").val(documentExpireDate);
            $("#document_issue_country_0").val(documentIssueCountry);
            $("#nationality_0").val(nationality);
            $("#frequent_flyer_no_0").val(frequentFlyerNo);
        }
    </script>
@endsection
