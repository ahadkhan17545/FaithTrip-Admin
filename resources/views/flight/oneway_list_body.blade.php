<div class="list-body col-md-7">
    <div class="d-none d-md-block">
        <h6 class="list-hidden mb-1 fs-13 font-weight-bold text-primary">One Way Trip</h6>
        <h6 class="align-items-center d-flex flex-wrap mb-2" style="font-size: 14px; line-height: 20px; font-weight: 600">
            @php
                $beginAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['beginAirport'];
                $beginAirportInfo = DB::table('city_airports')->where('airport_code', $beginAirportCode)->first();
                $endAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['endAirport'];
                $endAirportInfo = DB::table('city_airports')->where('airport_code', $endAirportCode)->first();
            @endphp

            <span>{{$beginAirportInfo->airport_name}}, {{$beginAirportInfo->city_name}}, {{$beginAirportInfo->country_name}} ({{$beginAirportInfo->airport_code}})</span>
            <svg class="bi bi-arrow-right  mx-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z"></path>
                <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z"></path>
            </svg>
            <span>
                {{$endAirportInfo ? $endAirportInfo->airport_name : ''}},
                {{$endAirportInfo ? $endAirportInfo->city_name : ''}},
                {{$endAirportInfo ? $endAirportInfo->country_name : ''}}
                ({{$endAirportInfo ? $endAirportInfo->airport_code : ''}})
            </span>
        </h6>
    </div>
    <div class="mb-2 d-none d-md-flex row">

        @php
            $legRef = $data['legs'][0]['ref'];
            $schedulesRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'][0]['ref'];
            $departureFlightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRef-1];

            $arrivalSchedulesRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'][count($searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'])-1]['ref'];
            $arrivalFlightTiming = $searchResults['groupedItineraryResponse']['scheduleDescs'][$arrivalSchedulesRef-1];

            // calculating total flight time1
            $totalFlightTiming = 0;
            $legRefArray = $data['legs'];

            $firstRawDepartureDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']." ".$departureFlightTiming['departure']['time'];

            foreach ($legRefArray as $legRefItem) {
                $schedulesRefArray = $searchResults['groupedItineraryResponse']['legDescs'][$legRefItem['ref']-1]['schedules'];
                foreach ($schedulesRefArray as $schedulesRefItem) {
                    $totalFlightTiming = $totalFlightTiming + $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRefItem['ref']-1]['elapsedTime'];

                    $date = new DateTime($firstRawDepartureDateTime);
                    $interval = new DateInterval('PT' . $searchResults['groupedItineraryResponse']['scheduleDescs'][$schedulesRefItem['ref']-1]['elapsedTime'] . 'M');
                    $date->add($interval);
                    $firstRawDepartureDateTime = $date->format('Y-m-d H:i:sP');
                }
            }
        @endphp

        <div class="d-flex align-items-center flight-icon col">
            <div class="fli-content">
                <div class="fli_title fs-14 mb-1 font-weight-600"><i class="fas fa-plane-departure"></i> Departure</div>
                <div class="fli-text fs-16">
                    @php
                        $rawDepartureDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']." ".$departureFlightTiming['departure']['time'];
                        $stringDepartureDateTime = new DateTime($rawDepartureDateTime);
                        $formattedDepartureDateTime = $stringDepartureDateTime->format('h:i a, jS M-Y');
                        echo $formattedDepartureDateTime;
                    @endphp
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center flight-icon col">
            <div class="fli-content">
                <div class="fli_title fs-14 mb-1 font-weight-600"><i class="fas fa-plane-arrival"></i> Arrival</div>
                <div class="fli-text fs-16">
                    @php
                        $stringArrivalDate = new DateTime($firstRawDepartureDateTime);
                        $formattedArrivalDate = $stringArrivalDate->format('jS M-Y');

                        $rawArrivalTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']." ".$arrivalFlightTiming['arrival']['time'];
                        $stringArrivalTime = new DateTime($rawArrivalTime);
                        $formattedArrivalTime = $stringArrivalTime->format('h:i a');
                        echo $formattedArrivalTime.", ".$formattedArrivalDate;
                    @endphp
                </div>
            </div>
        </div>
    </div>
    <div class="d-none d-md-flex align-items-sm-center text-center text-sm-left fs-14">
        <div class="fli-duration">
            <strong class="mr-1"><i class="far fa-clock"></i> {{App\Models\CustomFunction::convertMinToHrMin($totalFlightTiming)}}</strong>
        </div>
    </div>
</div>
