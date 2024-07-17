<div class="list-body col-md-7">
    <div class="row">
        <div class="col-12">
            <h6 class="list-hidden mb-1 fs-13 font-weight-500 text-primary">Round Trip</h6>
        </div>
    </div>

    @php
        $beginAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['beginAirport'];
        $beginAirportInfo = DB::table('city_airports')->where('airport_code', $beginAirportCode)->first();
        $endAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['endAirport'];
        $endAirportInfo = DB::table('city_airports')->where('airport_code', $endAirportCode)->first();

        $legRef = $data['legs'][0]['ref'];
        $schedulesRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'][0]['ref'];
        $flightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRef-1];
    @endphp

    <div class="row">
        <div class="col-4">
            <p class="mb-0 fs-14 font-weight-bold" style="font-weight: 600;">{{$beginAirportInfo->city_name}}, {{$beginAirportInfo->country_name}} ({{$beginAirportInfo->city_code}})</p>
            <p class="mb-0 fs-16">{{$searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']}} {{$flightTiming['departure']['time']}}</p>
        </div>
        <div class="col-4 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">NonStop</span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-4 text-right">
            <p class="mb-0 fs-14 font-weight-bold" style="font-weight: 600;">{{$endAirportInfo->city_name}}, {{$endAirportInfo->country_name}} ({{$endAirportInfo->city_code}})</p>
            <p class="mb-0 fs-16">{{$searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']}} {{$flightTiming['arrival']['time']}}</p>
        </div>
    </div>

    <hr>

    @php
        $beginAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][1]['beginAirport'];
        $beginAirportInfo = DB::table('city_airports')->where('airport_code', $beginAirportCode)->first();
        $endAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][1]['endAirport'];
        $endAirportInfo = DB::table('city_airports')->where('airport_code', $endAirportCode)->first();

        $legRef = $data['legs'][1]['ref'];
        $schedulesRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'][0]['ref'];
        $flightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRef-1];
    @endphp

    <div class="row">
        <div class="col-4">
            <p class="mb-0 fs-14 font-weight-bold" style="font-weight: 600;">{{$beginAirportInfo->city_name}}, {{$beginAirportInfo->country_name}} ({{$beginAirportInfo->city_code}})</p>
            <p class="mb-0 fs-16">{{$searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']}} {{$flightTiming['departure']['time']}}</p>
        </div>
        <div class="col-4 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">NonStop</span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-4 text-right">
            <p class="mb-0 fs-14 font-weight-bold" style="font-weight: 600;">{{$endAirportInfo->city_name}}, {{$endAirportInfo->country_name}} ({{$endAirportInfo->city_code}})</p>
            <p class="mb-0 fs-16">{{$searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']}} {{$flightTiming['arrival']['time']}}</p>
        </div>
    </div>

</div>
