<div class="col-lg-2 flight_airlines">
    <img class="img-fluid" src="{{ url('airlines_logo') }}/{{ strtolower($segmentArray[0]['carrier']['operating']) }}.png" loading="lazy">
    @php
        $opFlightCarrier = DB::table('airlines')
            ->where('iata', $segmentArray[0]['carrier']['operating'])
            ->where('active', 'Y')
            ->first();
    @endphp
    @if ($opFlightCarrier)
        <h5 class="text-center">{{ $opFlightCarrier->name }}</h5>
    @endif
    <h6>{{ $segmentArray[0]['carrier']['operatingFlightNumber'] }}-{{ $segmentArray[0]['carrier']['equipment']['code'] }}</h6>
</div>
<div class="col-lg-2 flight_timing">
    <h4>{{(new DateTimeImmutable($segmentArray[0]['departure']['dateTime']))->format("H:i")}}</h4>
    <h6>({{(new DateTimeImmutable($segmentArray[0]['departure']['dateTime']))->format("h:i A")}})</h6>
    <h5>{{ $segmentArray[0]['departure']['airport'] }}</h5>
    <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', $segmentArray[0]['departure']['airport'])->first()->city_name }}</h6>
</div>
<div class="col-lg-4 flight_duration">
    <i class="fas fa-plane"></i>
    <span>{{ App\Models\CustomFunction::convertMinToHrMin($totalFlightTiming) }}</span>

    @if (count($segmentArray) > 1)
        <div class="transit-container">
            @foreach ($segmentArray as $segmentIndex => $segmentData)
                @if ($segmentIndex > 0)
                    <div class="transit text-center">
                        @php
                            $lastLandedAt = $segmentArray[$segmentIndex - 1]['arrival']['dateTime'];
                            $willDepartureAt = $segmentArray[$segmentIndex]['departure']['dateTime'];

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
        @if (count($segmentArray) > 1)
            {{ count($segmentArray) - 1 }}
        @else
            Non
        @endif Stop
    </button>
</div>
<div class="col-lg-2 flight_timing">
    <h4>{{ (new DateTimeImmutable(end($segmentArray)['arrival']['dateTime']))->format("H:i") }}</h4>
    <h6>({{ (new DateTimeImmutable(end($segmentArray)['arrival']['dateTime']))->format("h:i A") }})</h6>
    <h5>{{ end($segmentArray)['arrival']['airport'] }}</h5>
    <h6 class="city_name">{{ DB::table('city_airports')->where('airport_code', end($segmentArray)['arrival']['airport'])->first()->city_name }}</h6>
</div>


<div class="col-lg-2 flight_price">
    <small>Gross:</small>
    <h5>৳ {{ number_format($data['pricingInformation'][0]['fare']['totalFare']['totalPrice']) }} </h5>
    <small>Net:</small>
    <h5>৳ {{ number_format($netPrice) }}</h5>
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
        <h6>
            {{ $segmentData['carrier']['operating'] }}-{{ $segmentData['carrier']['operatingFlightNumber'] }}:

            From <strong>{{ $segmentData['departure']['airport'] }}</strong>
            ({{(new DateTimeImmutable($segmentData['departure']['dateTime']))->format("d-M-y h:i A")}})

            To <strong>{{ $segmentData['arrival']['airport'] }}</strong>
            ({{ (new DateTimeImmutable($segmentData['arrival']['dateTime']))->format("d-M-y h:i A") }})
        </h6>
    @endforeach
</div>
