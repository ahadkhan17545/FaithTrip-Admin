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
            <div class="tab-pane fade show active" id="info_{{ $index }}" role="tabpanel" aria-labelledby="info-tab">

                @php
                    $segmentArray = [];
                    $legsArray = $data['legs'];
                    foreach ($legsArray as $key => $leg) {
                        $legRef = $leg['ref'] - 1;
                        $legDescription = $searchResults['groupedItineraryResponse']['legDescs'][$legRef];
                        $schedulesArray = $legDescription['schedules'];
                        foreach ($schedulesArray as $schedule) {
                            $scheduleRef = $schedule['ref'] - 1;
                            $segmentArray[] = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
                        }
                    }
                @endphp

                @foreach ($segmentArray as $segmentIndex => $segmentData)
                    <div class="flight-info border rounded mt-1 mb-1">
                        <div class="flight-scroll review-article">
                            <div class="align-items-center d-flex custom-gap justify-content-between w-100">
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
                                        {{ $segmentData['departure']['city'] }}
                                    </div>
                                    <span class="fs-12 font-weight-600">{{ $segmentData['departure']['time'] }}</span><br>
                                    <span class="text-muted fs-12">
                                        Terminal -
                                        {{ isset($segmentData['departure']['terminal']) ? $segmentData['departure']['terminal'] : 'N/A' }}
                                    </span>
                                </div>
                                <div class="text-center">
                                    <div class="font-weight-600 fs-13">
                                        {{ $segmentData['arrival']['city'] }}
                                    </div>
                                    <span class="fs-12 font-weight-600">{{ $segmentData['arrival']['time'] }}</span><br>
                                    <span class="text-muted fs-12">
                                        Terminal -
                                        {{ isset($segmentData['arrival']['terminal']) ? $segmentData['arrival']['terminal'] : 'N/A' }}
                                    </span>
                                </div>
                                <div class="text-center fs-14 w-100">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="d-inline-flex align-items-center w-max-content">
                                            {{ App\Models\CustomFunction::convertMinToHrMin($segmentData['elapsedTime']) }}
                                        </span>
                                        <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                            {{ isset($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode']) ? 'Meal - ' . $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode'] : 'N/A' }}
                                        </span>
                                        <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                            {{ isset($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode']) ? 'Booking Code - ' . $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'] : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="two-dots my-3 text-muted position-relative border-top">
                                        <span class="flight-service">
                                            <span class="type-text px-2 position-relative">Flight</span>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="d-inline-flex align-items-center w-max-content">
                                            @php
                                                $passangerWisebaggage = $data['pricingInformation'][0]['fare']['passengerInfoList'];

                                                foreach ($passangerWisebaggage as $passangerWisebaggageInfo) {
                                                    if (isset($passangerWisebaggageInfo['passengerInfo']['baggageInformation'][0]['allowance']['ref'])) {

                                                        $baggageRef = $passangerWisebaggageInfo['passengerInfo']['baggageInformation'][0]['allowance']['ref'];
                                                        if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1])) {
                                                            echo $passangerWisebaggageInfo['passengerInfo']['passengerType'] . '(' . $passangerWisebaggageInfo['passengerInfo']['passengerNumber'] . '): ';

                                                            if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'])) {
                                                                echo 'Piece Count: ' . $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'] * $passangerWisebaggageInfo['passengerInfo']['passengerNumber'];
                                                            }
                                                            if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'])) {
                                                                echo $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'] * $passangerWisebaggageInfo['passengerInfo']['passengerNumber'];
                                                            }
                                                            if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'])) {
                                                                echo ' ' . $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'];
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

                    @if(isset($segmentArray[$segmentIndex+1]) && isset($segmentData['arrival']['time']) && $segmentArray[$segmentIndex+1]['departure']['time'])
                    <div class="d-flex justify-center px-3">
                        <span class="fs-12 layover text-center">
                            @php
                                $time1 = substr($segmentData['arrival']['time'],0,8);
                                $time2 = substr($segmentArray[$segmentIndex+1]['departure']['time'],0,8);
                                $time1Obj = DateTime::createFromFormat('H:i:s', $time1);
                                $time2Obj = DateTime::createFromFormat('H:i:s', $time2);
                                $interval = $time1Obj->diff($time2Obj);
                                $formattedDifference = sprintf(
                                    "%dhr %dmin",
                                    $interval->h + ($interval->days * 24), // Total hours, including days if any
                                    $interval->i // Minutes
                                );
                                echo $formattedDifference." Layover";
                            @endphp
                        </span>
                    </div>
                    @endif
                @endforeach

            </div>
            <div class="tab-pane fade" id="fare-getails_{{ $index }}" role="tabpanel" aria-labelledby="fare-getails-tab">
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
                                                {{ $passengerData['passengerInfo']['passengerType'] }}</b>&nbsp;
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
            <div class="tab-pane fade" id="baggage_{{ $index }}" role="tabpanel" aria-labelledby="baggage-tab">
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
            <div class="tab-pane fade" id="cancellation_{{ $index }}" role="tabpanel" aria-labelledby="cancellation-tab">
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
