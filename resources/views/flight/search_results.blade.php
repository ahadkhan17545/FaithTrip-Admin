@extends('master')

@section('content')

    <div class="row">
        <div class="search-content-wrap m-auto">

            @if (isset($searchResults['groupedItineraryResponse']))
                <div class="sorting my-3">
                    <div class="badge bg-primary fs-16 mb-2 mb-lg-0">
                        Total
                        <span class="font-weight-500">
                            <b
                                id="total_flights">{{ $searchResults['groupedItineraryResponse']['statistics']['itineraryCount'] }}</b>
                        </span>
                        Flights found
                    </div>
                </div>

                <div class="layer position-fixed top-0 left-0 w-100"></div>

                <div class="row">
                    <div class="col-lg-9 mainContent">
                        <div class="theiaStickySidebar">

                            <div class="alert alert-primary">
                                <div class="align-items-center g-3 row">
                                    <div class="accordion" id="accordionExample" style="padding: 0">
                                        <div class="accordion-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-10 col-sm-12">
                                                    <div class="fs-15">
                                                        <span class="fw-bold">
                                                            @if (count($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions']) == 1)
                                                                One Way :
                                                            @else
                                                                Round :
                                                            @endif
                                                        </span>
                                                        <span class="ml-1">

                                                            @foreach ($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'] as $data)
                                                                @php
                                                                    $departureLocation = DB::table('city_airports')
                                                                        ->where('city_code', $data['departureLocation'])
                                                                        ->first();
                                                                    $arrivalLocation = DB::table('city_airports')
                                                                        ->where('city_code', $data['arrivalLocation'])
                                                                        ->first();
                                                                @endphp
                                                                {{ $departureLocation->city_name }},
                                                                {{ $departureLocation->country_name }}
                                                                ({{ $departureLocation->city_code }})
                                                                <i class="fas fa-plane-departure"></i>
                                                                {{ $arrivalLocation->city_name }},
                                                                {{ $arrivalLocation->country_name }}
                                                                ({{ $arrivalLocation->city_code }}),
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                {{ date('d-m-Y', strtotime($data['departureDate'])) }}
                                                            @endforeach

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-12">

                                                    <h2 class="accordion-header" id="headingOne">
                                                        <button class="btn btn-primary" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                            aria-expanded="true" aria-controls="collapseOne">
                                                            Modify search
                                                        </button>
                                                    </h2>

                                                </div>
                                            </div>

                                            <div id="collapseOne" class="accordion-collapse collapse"
                                                aria-labelledby="headingOne" data-bs-parent="#accordionExample"
                                                style="border:none;">
                                                <div class="accordion-body" style="padding: 0">
                                                    @include('flight.modify_search_results')
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="list-content" id="flight-infos">

                                @foreach ($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'] as $index => $data)
                                    <div class="search_result">
                                        <div class="bg-white hox list-item mb-3 rounded position-relative demo NonStop">

                                            <div class="m-0 align-items-center row">
                                                <div class="list-item_start text-center col-md-2">
                                                    <div class="d-flex d-md-block justify-content-center">
                                                        <div class="list-item_logo">
                                                            <img class="img-fluid"
                                                                src="{{ url('airlines_logo') }}/{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}.png"
                                                                alt="{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}">
                                                            <small class="d-block mt-1">
                                                                @php
                                                                    $airlineInfo = DB::table('airlines')
                                                                        ->where(
                                                                            'iata',
                                                                            $data['pricingInformation'][0]['fare'][
                                                                                'validatingCarrierCode'
                                                                            ],
                                                                        )
                                                                        ->where('active', 'Y')
                                                                        ->first();
                                                                    echo $airlineInfo ? $airlineInfo->name : '';
                                                                @endphp
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>


                                                @if (count($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions']) == 1)
                                                    @include('flight.oneway_list_body')
                                                @else
                                                    @include('flight.round_list_body')
                                                @endif


                                                <div
                                                    class="list-item_end title hotel-right clearfix grid-hidden d-flex align-items-center d-md-block mt-2 pt-2 col-md-3 justify-content-center">
                                                    <div
                                                        class="price-area d-xl-flex align-items-xl-center justify-content-xl-center text-center">
                                                        <div class="purchase-price fs-24 font-weight-600">
                                                            <div class="main-price">
                                                                {{ $data['pricingInformation'][0]['fare']['totalFare']['currency'] }}
                                                                {{ $data['pricingInformation'][0]['fare']['totalFare']['totalPrice'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex d-md-block gap-4 ml-auto ms-3 mt-md-3 text-center">
                                                        <a href="book-now.html" id="BookNowBtn hox"
                                                            class="btn btn-primary text-uppercase font-weight-600 fs-13 btn_filters_responsive disable_book_now_cls">Book
                                                            now</a>
                                                        <a class="fli-det-link text-muted fs-14 gap-2 d-block mt-md-2 d-flex align-items-center justify-content-center mr-2 mr-md-0"
                                                            data-bs-toggle="collapse" href="#collapse1_{{ $index }}"
                                                            role="button" aria-expanded="false" aria-controls="collapse1">
                                                            Flight details
                                                            <i class="fas fa-chevron-circle-down ml-1 text-success"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapse1_{{ $index }}">
                                                <div class="bg-white fli-det card card-body rounded-0 border-0 p-0 pt-3 px-md-3 py-md-3">
                                                    <ul class="nav nav-tabs border-bottom-0  mb-3" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <a class="nav-link rounded fs-14 font-weight-500 active" id="info-tab" data-bs-toggle="tab" href="#info_{{ $index }}" role="tab" aria-controls="info" aria-selected="true">Flight info</a>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <a class="nav-link rounded fs-14 font-weight-500" id="fare-getails-tab" data-bs-toggle="tab" href="#fare-getails_{{ $index }}" role="tab" aria-controls="home" aria-selected="true">Fare details</a>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <a class="nav-link rounded fs-14 font-weight-500"
                                                                id="baggage-tab" data-bs-toggle="tab"
                                                                href="#baggage_{{ $index }}" role="tab"
                                                                aria-controls="baggage" aria-selected="false">Baggage</a>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <a class="nav-link rounded fs-14 font-weight-500"
                                                                id="cancellation-tab" data-bs-toggle="tab"
                                                                href="#cancellation_{{ $index }}" role="tab"
                                                                aria-controls="cancellation"
                                                                aria-selected="false">Cancellation</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade show active"
                                                            id="info_{{ $index }}" role="tabpanel"
                                                            aria-labelledby="info-tab">

                                                            @php
                                                                $segmentArray = [];
                                                                $legsArray = $data['legs'];
                                                                foreach ($legsArray as $key => $leg) {
                                                                    $legRef = $leg['ref'] - 1;
                                                                    $legDescription =
                                                                        $searchResults['groupedItineraryResponse'][
                                                                            'legDescs'
                                                                        ][$legRef];
                                                                    $schedulesArray = $legDescription['schedules'];
                                                                    foreach ($schedulesArray as $schedule) {
                                                                        $scheduleRef = $schedule['ref'] - 1;
                                                                        $segmentArray[] =
                                                                            $searchResults['groupedItineraryResponse'][
                                                                                'scheduleDescs'
                                                                            ][$scheduleRef];
                                                                    }
                                                                }
                                                            @endphp

                                                            @foreach ($segmentArray as $segmentIndex => $segmentData)
                                                                <div class="flight-info border rounded mb-2">
                                                                    <div class="flight-scroll review-article">
                                                                        <div
                                                                            class="align-items-center d-flex custom-gap justify-content-between w-100">
                                                                            <div
                                                                                class="align-items-center d-flex gap-4 text-center">
                                                                                <div class="brand-img">
                                                                                    <img
                                                                                        src="{{ url('airlines_logo') }}/{{ $segmentData['carrier']['operating'] }}.png">
                                                                                </div>
                                                                                <div class="airline-box">
                                                                                    <div class="font-weight-600 fs-13">
                                                                                        {{ $segmentData['carrier']['operating'] }}
                                                                                    </div>
                                                                                    <div
                                                                                        class="font-weight-600 fs-13 text-muted w-max-content">
                                                                                        {{ $segmentData['carrier']['operatingFlightNumber'] }}
                                                                                        -
                                                                                        {{ $segmentData['carrier']['equipment']['code'] }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-center">
                                                                                <div class="font-weight-600 fs-13">
                                                                                    {{ $segmentData['departure']['city'] }}
                                                                                </div>
                                                                                <span
                                                                                    class="fs-12 font-weight-600">{{ $segmentData['departure']['time'] }}</span><br>
                                                                                <span class="text-muted fs-12">
                                                                                    Terminal -
                                                                                    {{ isset($segmentData['departure']['terminal']) ? $segmentData['departure']['terminal'] : 'N/A' }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="text-center">
                                                                                <div class="font-weight-600 fs-13">
                                                                                    {{ $segmentData['arrival']['city'] }}
                                                                                </div>
                                                                                <span
                                                                                    class="fs-12 font-weight-600">{{ $segmentData['arrival']['time'] }}</span><br>
                                                                                <span class="text-muted fs-12">
                                                                                    Terminal -
                                                                                    {{ isset($segmentData['arrival']['terminal']) ? $segmentData['arrival']['terminal'] : 'N/A' }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="text-center fs-14 w-100">
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-center">
                                                                                    <span
                                                                                        class="d-inline-flex align-items-center w-max-content">
                                                                                        {{ App\Models\CustomFunction::convertMinToHrMin($segmentData['elapsedTime']) }}
                                                                                    </span>
                                                                                    <span
                                                                                        class="d-inline-flex align-items-center w-max-content">
                                                                                        &nbsp;<span
                                                                                            class="text-muted">|</span>&nbsp;
                                                                                        {{ isset($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][$segmentIndex]['segments'][$segmentIndex]['segment']['mealCode']) ? 'Meal - ' . $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][$segmentIndex]['segments'][$segmentIndex]['segment']['mealCode'] : 'N/A' }}
                                                                                    </span>
                                                                                    <span
                                                                                        class="d-inline-flex align-items-center w-max-content">
                                                                                        &nbsp;<span
                                                                                            class="text-muted">|</span>&nbsp;
                                                                                        {{ isset($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][$segmentIndex]['segments'][$segmentIndex]['segment']['bookingCode']) ? 'Booking Code - ' . $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][$segmentIndex]['segments'][$segmentIndex]['segment']['bookingCode'] : 'N/A' }}
                                                                                    </span>
                                                                                </div>
                                                                                <div
                                                                                    class="two-dots my-3 text-muted position-relative border-top">
                                                                                    <span class="flight-service">
                                                                                        <span
                                                                                            class="type-text px-2 position-relative">Flight</span>
                                                                                    </span>
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-center">
                                                                                    <span
                                                                                        class="d-inline-flex align-items-center w-max-content">
                                                                                        @php
                                                                                            $passangerWisebaggage =
                                                                                                $data[
                                                                                                    'pricingInformation'
                                                                                                ][0]['fare'][
                                                                                                    'passengerInfoList'
                                                                                                ];

                                                                                            foreach (
                                                                                                $passangerWisebaggage
                                                                                                as $passangerWisebaggageInfo
                                                                                            ) {
                                                                                                if (
                                                                                                    isset(
                                                                                                        $passangerWisebaggageInfo[
                                                                                                            'passengerInfo'
                                                                                                        ][
                                                                                                            'baggageInformation'
                                                                                                        ][0][
                                                                                                            'allowance'
                                                                                                        ]['ref'],
                                                                                                    )
                                                                                                ) {
                                                                                                    $baggageRef =
                                                                                                        $passangerWisebaggageInfo[
                                                                                                            'passengerInfo'
                                                                                                        ][
                                                                                                            'baggageInformation'
                                                                                                        ][0][
                                                                                                            'allowance'
                                                                                                        ]['ref'];
                                                                                                    if (
                                                                                                        isset(
                                                                                                            $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ],
                                                                                                        )
                                                                                                    ) {
                                                                                                        echo $passangerWisebaggageInfo[
                                                                                                            'passengerInfo'
                                                                                                        ][
                                                                                                            'passengerType'
                                                                                                        ] .
                                                                                                            '(' .
                                                                                                            $passangerWisebaggageInfo[
                                                                                                                'passengerInfo'
                                                                                                            ][
                                                                                                                'passengerNumber'
                                                                                                            ] .
                                                                                                            '): ';

                                                                                                        if (
                                                                                                            isset(
                                                                                                                $searchResults[
                                                                                                                    'groupedItineraryResponse'
                                                                                                                ][
                                                                                                                    'baggageAllowanceDescs'
                                                                                                                ][
                                                                                                                    $baggageRef -
                                                                                                                        1
                                                                                                                ][
                                                                                                                    'pieceCount'
                                                                                                                ],
                                                                                                            )
                                                                                                        ) {
                                                                                                            echo 'Piece Count: ' .
                                                                                                                $searchResults[
                                                                                                                    'groupedItineraryResponse'
                                                                                                                ][
                                                                                                                    'baggageAllowanceDescs'
                                                                                                                ][
                                                                                                                    $baggageRef -
                                                                                                                        1
                                                                                                                ][
                                                                                                                    'pieceCount'
                                                                                                                ] *
                                                                                                                    $passangerWisebaggageInfo[
                                                                                                                        'passengerInfo'
                                                                                                                    ][
                                                                                                                        'passengerNumber'
                                                                                                                    ];
                                                                                                        }
                                                                                                        if (
                                                                                                            isset(
                                                                                                                $searchResults[
                                                                                                                    'groupedItineraryResponse'
                                                                                                                ][
                                                                                                                    'baggageAllowanceDescs'
                                                                                                                ][
                                                                                                                    $baggageRef -
                                                                                                                        1
                                                                                                                ][
                                                                                                                    'weight'
                                                                                                                ],
                                                                                                            )
                                                                                                        ) {
                                                                                                            echo $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ][
                                                                                                                'weight'
                                                                                                            ] *
                                                                                                                $passangerWisebaggageInfo[
                                                                                                                    'passengerInfo'
                                                                                                                ][
                                                                                                                    'passengerNumber'
                                                                                                                ];
                                                                                                        }
                                                                                                        if (
                                                                                                            isset(
                                                                                                                $searchResults[
                                                                                                                    'groupedItineraryResponse'
                                                                                                                ][
                                                                                                                    'baggageAllowanceDescs'
                                                                                                                ][
                                                                                                                    $baggageRef -
                                                                                                                        1
                                                                                                                ][
                                                                                                                    'unit'
                                                                                                                ],
                                                                                                            )
                                                                                                        ) {
                                                                                                            echo ' ' .
                                                                                                                $searchResults[
                                                                                                                    'groupedItineraryResponse'
                                                                                                                ][
                                                                                                                    'baggageAllowanceDescs'
                                                                                                                ][
                                                                                                                    $baggageRef -
                                                                                                                        1
                                                                                                                ][
                                                                                                                    'unit'
                                                                                                                ];
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

                                                                    {{-- <div class="d-flex justify-center px-3">
                                                                        <span class="fs-12 layover text-center">17hr 30min Layover</span>
                                                                    </div> --}}
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                        <div class="tab-pane fade" id="fare-getails_{{ $index }}"
                                                            role="tabpanel" aria-labelledby="fare-getails-tab">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <table
                                                                        class="table table-bordered table-sm mb-0 text-center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><b>Passenger type</b></td>
                                                                                <td><b>Base fare</b></td>
                                                                                <td><b>Tax fare</b></td>
                                                                                <td><b>Total fare</b></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    @foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
                                                                                        <b>{{ $passengerData['passengerInfo']['passengerNumber'] }}
                                                                                            {{ $passengerData['passengerInfo']['passengerType'] }}</b><br>
                                                                                    @endforeach
                                                                                </td>
                                                                                <td>
                                                                                    {{ $data['pricingInformation'][0]['fare']['totalFare']['baseFareAmount'] }}
                                                                                    ({{ $data['pricingInformation'][0]['fare']['totalFare']['baseFareCurrency'] }})
                                                                                </td>
                                                                                <td>
                                                                                    {{ $data['pricingInformation'][0]['fare']['totalFare']['totalTaxAmount'] }}
                                                                                    ({{ $data['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                                                                </td>
                                                                                <td>
                                                                                    {{ $data['pricingInformation'][0]['fare']['totalFare']['totalPrice'] }}
                                                                                    ({{ $data['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="baggage_{{ $index }}"
                                                            role="tabpanel" aria-labelledby="baggage-tab">
                                                            <div class="row">
                                                                <div class="col-md-6 pr-md-2">
                                                                    <table
                                                                        class="table table-bordered table-sm mb-0 text-center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>Passenger type</td>
                                                                                <td>Weight</td>
                                                                                <td>Provision Type</td>
                                                                            </tr>
                                                                            @foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
                                                                                <tr>
                                                                                    <td>{{ $passengerData['passengerInfo']['passengerNumber'] }}
                                                                                        {{ $passengerData['passengerInfo']['passengerType'] }}
                                                                                    </td>
                                                                                    <td>
                                                                                        @php
                                                                                            if (
                                                                                                isset(
                                                                                                    $passengerData[
                                                                                                        'passengerInfo'
                                                                                                    ][
                                                                                                        'baggageInformation'
                                                                                                    ][0]['allowance'][
                                                                                                        'ref'
                                                                                                    ],
                                                                                                )
                                                                                            ) {
                                                                                                $baggageRef =
                                                                                                    $passengerData[
                                                                                                        'passengerInfo'
                                                                                                    ][
                                                                                                        'baggageInformation'
                                                                                                    ][0]['allowance'][
                                                                                                        'ref'
                                                                                                    ];
                                                                                                if (
                                                                                                    isset(
                                                                                                        $searchResults[
                                                                                                            'groupedItineraryResponse'
                                                                                                        ][
                                                                                                            'baggageAllowanceDescs'
                                                                                                        ][
                                                                                                            $baggageRef -
                                                                                                                1
                                                                                                        ],
                                                                                                    )
                                                                                                ) {
                                                                                                    if (
                                                                                                        isset(
                                                                                                            $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ][
                                                                                                                'pieceCount'
                                                                                                            ],
                                                                                                        )
                                                                                                    ) {
                                                                                                        echo 'Piece Count: ' .
                                                                                                            $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ][
                                                                                                                'pieceCount'
                                                                                                            ] *
                                                                                                                $passengerData[
                                                                                                                    'passengerInfo'
                                                                                                                ][
                                                                                                                    'passengerNumber'
                                                                                                                ];
                                                                                                    }
                                                                                                    if (
                                                                                                        isset(
                                                                                                            $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ]['weight'],
                                                                                                        )
                                                                                                    ) {
                                                                                                        echo $searchResults[
                                                                                                            'groupedItineraryResponse'
                                                                                                        ][
                                                                                                            'baggageAllowanceDescs'
                                                                                                        ][
                                                                                                            $baggageRef -
                                                                                                                1
                                                                                                        ]['weight'] *
                                                                                                            $passengerData[
                                                                                                                'passengerInfo'
                                                                                                            ][
                                                                                                                'passengerNumber'
                                                                                                            ];
                                                                                                    }
                                                                                                    if (
                                                                                                        isset(
                                                                                                            $searchResults[
                                                                                                                'groupedItineraryResponse'
                                                                                                            ][
                                                                                                                'baggageAllowanceDescs'
                                                                                                            ][
                                                                                                                $baggageRef -
                                                                                                                    1
                                                                                                            ]['unit'],
                                                                                                        )
                                                                                                    ) {
                                                                                                        echo $searchResults[
                                                                                                            'groupedItineraryResponse'
                                                                                                        ][
                                                                                                            'baggageAllowanceDescs'
                                                                                                        ][
                                                                                                            $baggageRef -
                                                                                                                1
                                                                                                        ]['unit'];
                                                                                                    }

                                                                                                    echo '&nbsp;';
                                                                                                }
                                                                                            }
                                                                                        @endphp
                                                                                    </td>
                                                                                    <td>
                                                                                        @if (isset($passengerData['passengerInfo']['baggageInformation'][0]['provisionType']))
                                                                                            {{ $passengerData['passengerInfo']['baggageInformation'][0]['provisionType'] }}
                                                                                        @endif
                                                                                        @if (isset($passengerData['passengerInfo']['baggageInformation'][0]['airlineCode']))
                                                                                            -&nbsp;{{ $passengerData['passengerInfo']['baggageInformation'][0]['airlineCode'] }}
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6  pl-md-2">
                                                                    <p class="mb-0">The baggage information is just for
                                                                        reference please check with airline before check in
                                                                        for more information visit airlines website</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="cancellation_{{ $index }}"
                                                            role="tabpanel" aria-labelledby="cancellation-tab">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class="table table-bordered table-sm text-center">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Passenger Type</td>
                                                                                    <td>Non Refundable</td>
                                                                                    <td>Class</td>
                                                                                    <td>Available Seats</td>
                                                                                    <td>Booking Code</td>
                                                                                    <td>Meal Code</td>
                                                                                </tr>
                                                                                @foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
                                                                                    <tr>
                                                                                        <td>{{ $passengerData['passengerInfo']['passengerNumber'] }}
                                                                                            {{ $passengerData['passengerInfo']['passengerType'] }}
                                                                                        </td>
                                                                                        <td
                                                                                            style="text-transform: capitalize;">
                                                                                            @php
                                                                                                echo json_encode(
                                                                                                    $passengerData[
                                                                                                        'passengerInfo'
                                                                                                    ]['nonRefundable'],
                                                                                                );
                                                                                            @endphp
                                                                                        </td>
                                                                                        <td>Economy</td>
                                                                                        <td>
                                                                                            @foreach ($passengerData['passengerInfo']['fareComponents'][0]['segments'] as $itemIndex => $segment)
                                                                                                Segment-{{ $itemIndex + 1 }}:
                                                                                                {{ $segment['segment']['seatsAvailable'] }}&nbsp;
                                                                                            @endforeach
                                                                                        </td>
                                                                                        <td>
                                                                                            @foreach ($passengerData['passengerInfo']['fareComponents'][0]['segments'] as $itemIndex => $segment)
                                                                                                Segment-{{ $itemIndex + 1 }}:
                                                                                                {{ $segment['segment']['bookingCode'] }}&nbsp;
                                                                                            @endforeach
                                                                                        </td>
                                                                                        <td>
                                                                                            @foreach ($passengerData['passengerInfo']['fareComponents'][0]['segments'] as $itemIndex => $segment)
                                                                                                @if (isset($segment['segment']['mealCode']))
                                                                                                    Segment-{{ $itemIndex + 1 }}:
                                                                                                    {{ $segment['segment']['mealCode'] }}&nbsp;
                                                                                                @else
                                                                                                    N/A
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <hr>

                                                                <div class="col-md-12">
                                                                    <h6 class="my-3">Return and refund policy </h6>
                                                                    <table
                                                                        class="table table-bordered table-sm text-center">
                                                                        <tbody>
                                                                            <tr class="font-weight-bold">
                                                                                <td>Type</td>
                                                                                <td>Changeable before departure</td>
                                                                                <td>Penalty</td>
                                                                                <td>Changeable after departure</td>
                                                                                <td>Penalty</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <h6>Terms &amp; Conditions</h6>
                                                            <ul>
                                                                <li>The charges are per passenger per sector and applicable
                                                                    only on refundable type tickets.</li>
                                                                <li>Rescheduling Charges = Rescheduling/Change Penalty +
                                                                    Fare Difference (if applicable)</li>
                                                                <li>Partial cancellation is not allowed on tickets booked
                                                                    under special discounted fares.</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-lg-block leftSidebar mb-3">
                        <div class="theiaStickySidebar">
                            @include('flight.filter_search_results')
                        </div>
                    </div>

                </div>
            @else
                {{-- if no flights found --}}
                <div class="sorting my-3">
                    <div class="badge bg-primary fs-16 mb-2 mb-lg-0 w-100 p-3 mt-4">
                        Sorry! No Flights found &nbsp;&nbsp;
                        <a href="{{ url('/') }}" class="d-inline btn btn-sm btn-rounded"
                            style="background: #ffffffe8; font-weight: 600;">Search Again</a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
