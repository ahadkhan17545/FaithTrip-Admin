@php
    $airlineInfo = DB::table('airlines')
        ->where('iata', $data['pricingInformation'][0]['fare']['validatingCarrierCode'])
        ->where('active', 'Y')
        ->first();

    $segmentArray = [];
    $legsArray = $data['legs'];
    foreach ($legsArray as $key => $leg) {
        $legRef = $leg['ref'] - 1;
        $legDescription = $searchResults['groupedItineraryResponse']['legDescs'][$legRef];
        $schedulesArray = $legDescription['schedules'];
        foreach ($schedulesArray as $schedulesArrayIndex => $schedule) {
            $scheduleRef = $schedule['ref'] - 1;
            $segmentArray[] = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
            if (isset($schedule['departureDateAdjustment'])) {
                $segmentArray[$schedulesArrayIndex]['bothDateAdjustment'] = $schedule['departureDateAdjustment'];
            }
        }
    }

    $beginAirportCode =
        $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0][
            'beginAirport'
        ];
    $beginAirportInfo = DB::table('city_airports')->where('airport_code', $beginAirportCode)->first();
    $endAirportCode =
        $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0][
            'endAirport'
        ];
    $endAirportInfo = DB::table('city_airports')->where('airport_code', $endAirportCode)->first();

    // timing related calculation
    $legRef = $data['legs'][0]['ref'];
    $schedulesRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef - 1]['schedules'][0]['ref'];
    $departureFlightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRef - 1];

    $arrivalSchedulesRef =
        $searchResults['groupedItineraryResponse']['legDescs'][$legRef - 1]['schedules'][
            count($searchResults['groupedItineraryResponse']['legDescs'][$legRef - 1]['schedules']) - 1
        ]['ref'];
    $arrivalFlightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$arrivalSchedulesRef - 1];

    // calculating total flight time1
    $totalFlightTiming = 0;
    $legRefArray = $data['legs'];

    $firstRawDepartureDateTime =
        $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0][
            'departureDate'
        ] .
        ' ' .
        $departureFlightTiming['departure']['time'];

    foreach ($legRefArray as $legRefItem) {
        $schedulesRefArray =
            $searchResults['groupedItineraryResponse']['legDescs'][$legRefItem['ref'] - 1]['schedules'];
        foreach ($schedulesRefArray as $schedulesRefItem) {
            $totalFlightTiming =
                $totalFlightTiming +
                $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRefItem['ref'] - 1][
                    'elapsedTime'
                ];

            $date = new DateTime($firstRawDepartureDateTime);
            $interval = new DateInterval(
                'PT' .
                    $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRefItem['ref'] - 1][
                        'elapsedTime'
                    ] .
                    'M',
            );
            $date->add($interval);
            $firstRawDepartureDateTime = $date->format('Y-m-d H:i:sP');
        }
    }

    // for departure date time
    $rawDepartureDateTime =
        $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0][
            'departureDate'
        ] .
        ' ' .
        $departureFlightTiming['departure']['time'];
    $stringDepartureDateTime = new DateTime($rawDepartureDateTime);

    // for arrival date time
    $stringArrivalDate = new DateTime($firstRawDepartureDateTime);
    $formattedArrivalDate = $stringArrivalDate->format('jS M-Y');
    $rawArrivalTime =
        $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0][
            'departureDate'
        ] .
        ' ' .
        $arrivalFlightTiming['arrival']['time'];
    $stringArrivalTime = new DateTime($rawArrivalTime);

    // price related calculation
    $netPrice = $data['pricingInformation'][0]['fare']['totalFare']['totalPrice'];
    $basePrice = $data['pricingInformation'][0]['fare']['totalFare']['equivalentAmount'];
    if (Auth::user()->user_type == 2) {
        if ($airlineInfo && $airlineInfo->comission > 0) {
            // if airline has comission
            $b2bUsersComission = Auth::user()->comission;
            if (!empty($b2bUsersComission) && is_numeric($b2bUsersComission) && $b2bUsersComission > 0) {
                $comissionAmount = round(($basePrice * $b2bUsersComission) / 100, 2);
                $netPrice -= $comissionAmount;
            }
        }
    } else {
        if ($airlineInfo && $airlineInfo->comission > 0) {
            // if airline has comission
            $comissionAmount = round(($basePrice * 7) / 100, 2);
            $netPrice -= $comissionAmount;
        }
    }

    $departureDate = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'];
    $arrivalDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'];
@endphp

<div class="row flight_card">
    <div class="col-lg-2 flight_airlines">
        <img class="img-fluid"
            src="{{ url('airlines_logo') }}/{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}.png"
            alt="{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}" loading="lazy">
        @if ($airlineInfo)
            <h5 class="text-center">{{ $airlineInfo->name }}</h5>
        @endif
        <h6>{{ $segmentArray[0]['carrier']['operatingFlightNumber'] }}-{{ $segmentArray[0]['carrier']['equipment']['code'] }}
        </h6>
    </div>
    <div class="col-lg-2 flight_timing">
        <h4>{{ $stringDepartureDateTime->format('h:i') }}</h4>
        <h6>({{ $stringDepartureDateTime->format('h:i a') }})</h6>

        <h5>{{ $beginAirportCode }}</h5>
        <h6 class="city_name">{{ $beginAirportInfo->city_name }}</h6>
    </div>
    <div class="col-lg-4 flight_duration">
        <i class="fas fa-plane"></i>
        <span>{{ App\Models\CustomFunction::convertMinToHrMin($totalFlightTiming) }}</span>



        @if (count($segmentArray) > 1)
            <div class="transit-container">

                @php
                    $departureDateArray = [];
                    $arrivalDateArray = [];
                @endphp

                @foreach ($segmentArray as $segmentIndex => $segmentData)

                    @php
                        $departureDateTime = new DateTime($departureDate . ' ' . $segmentData['departure']['time']);
                        $arrivalDateTime = new DateTime($departureDate . ' ' . $segmentData['arrival']['time']);

                        if(isset($segmentData['bothDateAdjustment']) && $segmentData['bothDateAdjustment'] >= 1){
                            $departureDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
                            $arrivalDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
                        } else {
                            // Adjust the departure date if there's a date adjustment only for departure
                            if (isset($segmentData['departure']['dateAdjustment']) && $segmentData['departure']['dateAdjustment'] > 0) {
                                $departureDateTime->modify('+' . $segmentData['departure']['dateAdjustment'] . ' day');
                            }
                        }
                        $departureDateArray[] = $departureDateTime->format('d-M-y h:i A');
                        $arrivalDateArray[] = $arrivalDateTime->format('d-M-y h:i A');
                    @endphp

                    @if ($segmentIndex > 0)
                        <div class="transit text-center">
                            @php
                                $lastLandedAt = $arrivalDateArray[$segmentIndex - 1];
                                $willDepartureAt = $departureDateArray[$segmentIndex];
                                // Create DateTime objects
                                $lastLandedDatetime = DateTime::createFromFormat('d-M-y h:i A', $lastLandedAt);
                                $willDepartureDatetime = DateTime::createFromFormat('d-M-y h:i A', $willDepartureAt);
                                // Calculate the difference
                                $flightTransit = $lastLandedDatetime->diff($willDepartureDatetime);
                            @endphp
                            <span>{{$flightTransit->h}}hr {{$flightTransit->i}}min</span>
                            <h6>Transit at {{ $segmentData['departure']['airport'] }}</h6>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <button>
            @if (count($segmentArray) > 1)
                {{ count($segmentArray) - 1 }}
            @else
                Non
            @endif Stop
        </button>
    </div>
    <div class="col-lg-2 flight_timing">
        <h4>{{ $stringArrivalTime->format('h:i') }}</h4>
        <h6>({{ $stringArrivalTime->format('h:i a') }})</h6>

        <h5>{{ $endAirportCode }}</h5>
        <h6 class="city_name">{{ $endAirportInfo->city_name }}</h6>
    </div>
    <div class="col-lg-2 flight_price">
        <small>Gross:</small>
        <h5>৳ {{ number_format($data['pricingInformation'][0]['fare']['totalFare']['totalPrice']) }} </h5>
        <small>Net:</small>
        <h5>৳ {{ number_format($netPrice) }} </h5>
        <a href="{{ url('select/flight') }}/{{ $index }}">Select Flight</a>
    </div>
    <div class="col-lg-12 additional_info">
        <h6>
            {{-- Baggage and seats --}}
            @php
            foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData){
                if (isset($passengerData['passengerInfo']['baggageInformation'][0]['allowance']['ref'])) {
                    $baggageRef = $passengerData['passengerInfo']['baggageInformation'][0]['allowance']['ref'];
                    if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1])) {
                        if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'])) {
                            echo 'Baggage: Piece Count: ' .
                                $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'] *
                                    $passengerData['passengerInfo']['passengerNumber'].", ";
                        }
                        if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'])) {
                            echo "Baggage: ".$searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'] *
                                $passengerData['passengerInfo']['passengerNumber'];
                        }
                        if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'])) {
                            echo $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'].", ";
                        }
                    }
                }
            }

            foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData){
                foreach ($passengerData['passengerInfo']['fareComponents'][0]['segments'] as $itemIndex => $segment){
                    if($itemIndex ==0){ //only for the 1st segment
                        if(isset($segment['segment']['seatsAvailable'])){
                            echo "Seat: ".$segment['segment']['seatsAvailable'];
                        }
                        else{
                            echo "Seat: N/A";
                        }
                    }
                }
            }
            @endphp
        </h6>
        <h6>per passnger: BDT {{ number_format($data['pricingInformation'][0]['fare']['totalFare']['totalPrice']) }}</h6>
    </div>
    <div class="col-lg-12 additional_info mt-2 d-block">

        @foreach ($segmentArray as $segmentIndex => $segmentData)
            @php
                $departureDateTime = new DateTime($departureDate . ' ' . $segmentData['departure']['time']);
                $arrivalDateTime = new DateTime($departureDate . ' ' . $segmentData['arrival']['time']);

                if(isset($segmentData['bothDateAdjustment']) && $segmentData['bothDateAdjustment'] >= 1){
                    $departureDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
                    $arrivalDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
                } else {
                    // Adjust the departure date if there's a date adjustment only for departure
                    if (isset($segmentData['departure']['dateAdjustment']) && $segmentData['departure']['dateAdjustment'] > 0) {
                        $departureDateTime->modify('+' . $segmentData['departure']['dateAdjustment'] . ' day');
                    }
                }
            @endphp
            <h6>
                {{ $segmentData['carrier']['operating'] }}-{{ $segmentData['carrier']['operatingFlightNumber'] }}:

                From <strong>{{ $segmentData['departure']['airport'] }}</strong>
                ({{$departureDateTime->format('d-M-y h:i A')}})

                To <strong>{{ $segmentData['arrival']['airport'] }}</strong>
                ({{$arrivalDateTime->format('d-M-y h:i A')}})
            </h6>
        @endforeach
    </div>
</div>
