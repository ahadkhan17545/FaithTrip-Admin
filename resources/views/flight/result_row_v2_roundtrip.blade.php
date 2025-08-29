@php
    // Initialize empty arrays for step 0 and step 1
    $onewaySegmentArray = [];
    $roundTripSegmentArray = [];

    // Loop through the input array
    foreach ($segmentArray as $item) {
        if ($item['step'] === 0) {
            $onewaySegmentArray[] = $item;
        } elseif ($item['step'] === 1) {
            $roundTripSegmentArray[] = $item;
        }
    }
@endphp

<div class="row">
    <div class="col-lg-10">
        <div class="row">
            <div class="col-lg-3 flight_airlines">
                <img class="img-fluid" src="{{ url('airlines_logo') }}/{{ strtolower($onewaySegmentArray[0]['carrier']['operating']) }}.png" loading="lazy">
                @php
                    $opFlightCarrier = DB::table('airlines')
                        ->where('iata', $onewaySegmentArray[0]['carrier']['operating'])
                        ->where('active', 'Y')
                        ->first();
                @endphp
                @if ($opFlightCarrier)
                    <h5 class="text-center">{{ $opFlightCarrier->name }}</h5>
                @endif
                <h6>{{ $onewaySegmentArray[0]['carrier']['operatingFlightNumber'] }}-{{ $onewaySegmentArray[0]['carrier']['equipment']['code'] }}</h6>
            </div>
            <div class="col-lg-2 flight_timing">
                <h4>{{(new DateTimeImmutable($onewaySegmentArray[0]['departure']['dateTime']))->format("H:i")}}</h4>
                <h6>({{(new DateTimeImmutable($onewaySegmentArray[0]['departure']['dateTime']))->format("h:i A")}})</h6>

                <h5>{{ $onewaySegmentArray[0]['departure']['airport'] }}</h5>
                <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', $onewaySegmentArray[0]['departure']['airport'])->first()->city_name }}</h6>
            </div>
            <div class="col-lg-5 flight_duration">
                <i class="fas fa-plane"></i>
                @php
                    $totalFlightTiming = 0;
                    foreach ($onewaySegmentArray as $segmentData){
                        $totalFlightTiming += $segmentData['elapsedTime'];
                    }
                @endphp
                <span>{{ App\Models\CustomFunction::convertMinToHrMin($totalFlightTiming) }}</span>

                @if (count($onewaySegmentArray) > 1)
                    <div class="transit-container">
                        @foreach ($onewaySegmentArray as $onewaySegmentIndex => $segmentData)
                            @if ($onewaySegmentIndex > 0)
                                <div class="transit text-center">
                                    @php
                                        $lastLandedAt = $onewaySegmentArray[$onewaySegmentIndex - 1]['arrival']['dateTime'];
                                        $willDepartureAt = $onewaySegmentArray[$onewaySegmentIndex]['departure']['dateTime'];

                                        $date1 = new DateTime($lastLandedAt);
                                        $date2 = new DateTime($willDepartureAt);
                                        $differenceInMinutes = ($date2->getTimestamp() - $date1->getTimestamp()) / 60;
                                        $totalHours = intdiv($differenceInMinutes, 60);
                                        $totalMinutes = $differenceInMinutes % 60;
                                    @endphp
                                    <span>{{$totalHours}}hr {{$totalMinutes}}min</span>
                                    <h6>Transit at {{ $segmentData['departure']['airport'] }}</h6>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <button>
                    @if (count($onewaySegmentArray) > 1)
                        {{ count($onewaySegmentArray) - 1 }}
                    @else
                        Non
                    @endif Stop
                </button>
            </div>
            <div class="col-lg-2 flight_timing">
                <h4>{{ (new DateTimeImmutable(end($onewaySegmentArray)['arrival']['dateTime']))->format("H:i") }}</h4>
                <h6>({{ (new DateTimeImmutable(end($onewaySegmentArray)['arrival']['dateTime']))->format("h:i A") }})</h6>

                <h5>{{ end($onewaySegmentArray)['arrival']['airport'] }}</h5>
                <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', end($onewaySegmentArray)['arrival']['airport'])->first()->city_name }}</h6>
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
                                    echo 'Baggage: ' .
                                        $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'] *
                                            $passengerData['passengerInfo']['passengerNumber']." Piece, ";
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
                            if($itemIndex == 0){ //only for the 1st segment
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
            </div>
        </div>
        <div class="row mt-3 pt-3 border-top">
            <div class="col-lg-3 flight_airlines">
                <img class="img-fluid" src="{{ url('airlines_logo') }}/{{ strtolower($roundTripSegmentArray[0]['carrier']['operating']) }}.png" loading="lazy">
                @php
                    $opFlightCarrier = DB::table('airlines')
                        ->where('iata', $roundTripSegmentArray[0]['carrier']['operating'])
                        ->where('active', 'Y')
                        ->first();
                @endphp
                @if ($opFlightCarrier)
                    <h5 class="text-center">{{ $opFlightCarrier->name }}</h5>
                @endif
                <h6>{{ $roundTripSegmentArray[0]['carrier']['operatingFlightNumber'] }}-{{ $roundTripSegmentArray[0]['carrier']['equipment']['code'] }}</h6>
            </div>
            <div class="col-lg-2 flight_timing">
                <h4>{{(new DateTimeImmutable($roundTripSegmentArray[0]['departure']['dateTime']))->format("H:i")}}</h4>
                <h6>({{(new DateTimeImmutable($roundTripSegmentArray[0]['departure']['dateTime']))->format("h:i A")}})</h6>

                <h5>{{ $roundTripSegmentArray[0]['departure']['airport'] }}</h5>
                <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', $roundTripSegmentArray[0]['departure']['airport'])->first()->city_name }}</h6>
            </div>
            <div class="col-lg-5 flight_duration">
                <i class="fas fa-plane"></i>
                @php
                    $totalFlightTiming = 0;
                    foreach ($roundTripSegmentArray as $segmentData){
                        $totalFlightTiming += $segmentData['elapsedTime'];
                    }
                @endphp
                <span>{{ App\Models\CustomFunction::convertMinToHrMin($totalFlightTiming) }}</span>

                @if (count($roundTripSegmentArray) > 1)
                    <div class="transit-container">
                        @foreach ($roundTripSegmentArray as $roundTripSegmentIndex => $segmentData)
                            @if ($roundTripSegmentIndex > 0)
                                <div class="transit text-center">
                                    @php
                                        $lastLandedAt = $roundTripSegmentArray[$roundTripSegmentIndex - 1]['arrival']['dateTime'];
                                        $willDepartureAt = $roundTripSegmentArray[$roundTripSegmentIndex]['departure']['dateTime'];

                                        $date1 = new DateTime($lastLandedAt);
                                        $date2 = new DateTime($willDepartureAt);
                                        $differenceInMinutes = ($date2->getTimestamp() - $date1->getTimestamp()) / 60;
                                        $totalHours = intdiv($differenceInMinutes, 60);
                                        $totalMinutes = $differenceInMinutes % 60;
                                    @endphp
                                    <span>{{$totalHours}}hr {{$totalMinutes}}min</span>
                                    <h6>Transit at {{ $segmentData['departure']['airport'] }}</h6>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <button>
                    @if (count($roundTripSegmentArray) > 1)
                        {{ count($roundTripSegmentArray) - 1 }}
                    @else
                        Non
                    @endif Stop
                </button>
            </div>
            <div class="col-lg-2 flight_timing">
                <h4>{{ (new DateTimeImmutable(end($roundTripSegmentArray)['arrival']['dateTime']))->format("H:i") }}</h4>
                <h6>({{ (new DateTimeImmutable(end($roundTripSegmentArray)['arrival']['dateTime']))->format("h:i A") }})</h6>

                <h5>{{ end($roundTripSegmentArray)['arrival']['airport'] }}</h5>
                <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', end($roundTripSegmentArray)['arrival']['airport'])->first()->city_name }}</h6>
            </div>
            <div class="col-lg-12 additional_info">
                <h6>
                    {{-- Baggage and seats --}}
                    @php
                    foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData){
                        if (isset($passengerData['passengerInfo']['baggageInformation'][1]['allowance']['ref'])) {
                            $baggageRef = $passengerData['passengerInfo']['baggageInformation'][1]['allowance']['ref'];
                            if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1])) {
                                if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'])) {
                                    echo 'Baggage: ' .
                                        $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'] *
                                            $passengerData['passengerInfo']['passengerNumber']." Piece";
                                }
                                if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'])) {
                                    echo "Baggage: ".$searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'] *
                                        $passengerData['passengerInfo']['passengerNumber'];
                                }
                                if (isset($searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'])) {
                                    echo $searchResults['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'];
                                }
                            }
                        }
                    }

                    foreach ($data['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData){
                        foreach ($passengerData['passengerInfo']['fareComponents'][1]['segments'] as $itemIndex2 => $segment){
                            if($itemIndex2 == 0){ //only for the 1st segment of roundtrip
                                if(isset($segment['segment']['seatsAvailable'])){
                                    echo ", Seat: ".$segment['segment']['seatsAvailable'];
                                }
                                else{
                                    echo ", Seat: N/A";
                                }
                            }
                        }
                    }
                    @endphp
                </h6>
            </div>
        </div>
    </div>
    <div class="col-lg-2 flight_price">
        <small>Gross:</small>
        <h5>৳ {{ number_format($data['pricingInformation'][0]['fare']['totalFare']['totalPrice']) }} </h5>
        <small>Net:</small>
        <h5>৳ {{ number_format($netPrice) }} </h5>
        <a href="{{ url('select/flight') }}/{{ $index }}">Select Flight</a>
    </div>
</div>
<div class="col-lg-12 additional_info mt-2 d-block">
    @php
        $refundStatus = "";
        if(isset($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['nonRefundable'])){
            if($data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['nonRefundable'] == true)
                $refundStatus = "Yes";
            else
                $refundStatus = "No";
        }
    @endphp

    @if($refundStatus != "")
    <h6>Refundable: <span style="@if($refundStatus == "Yes") color: green; @else color: red; @endif font-weight: 600;">{{$refundStatus}}</span></h6>
    @endif

    @foreach ($segmentArray as $segmentIndex => $segmentData)
        <h6>
            {{ $segmentData['carrier']['operating'] }}-{{ $segmentData['carrier']['marketingFlightNumber'] }}:

            From <strong>{{ $segmentData['departure']['airport'] }}</strong>
            ({{(new DateTimeImmutable($segmentData['departure']['dateTime']))->format("d-M-y h:i A")}})

            To <strong>{{ $segmentData['arrival']['airport'] }}</strong>
            ({{ (new DateTimeImmutable($segmentData['arrival']['dateTime']))->format("d-M-y h:i A") }})
        </h6>
    @endforeach
</div>
