@php
    $segmentArray = [];
    $totalFlightTiming = 0;
    $legsArray = $data['legs'];
    foreach ($legsArray as $key => $leg) {

        $legRef = $leg['ref'] - 1;
        $legDescription = $searchResults['groupedItineraryResponse']['legDescs'][$legRef];
        $schedulesArray = $legDescription['schedules'];

        foreach ($schedulesArray as $schedule) {
            $scheduleRef = $schedule['ref'] - 1;
            $scheduleData = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
            $searchQueryDepartureDate = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][$key]['departureDate'];
            $totalFlightTiming += $scheduleData['elapsedTime'];

            $daysToBeAdded = 0;
            if (isset($schedule['departureDateAdjustment'])) {
                $daysToBeAdded = $schedule['departureDateAdjustment'];
                $scheduleData['departure']['dateTime'] = date("Y-m-d", strtotime("+".$daysToBeAdded." day", strtotime($searchQueryDepartureDate)))." ".$scheduleData['departure']['time'];
                $scheduleData['arrival']['dateTime'] = date("Y-m-d", strtotime("+".$daysToBeAdded." day", strtotime($searchQueryDepartureDate)))." ".$scheduleData['arrival']['time'];
            } else {
                $scheduleData['departure']['dateTime'] = $searchQueryDepartureDate." ".$scheduleData['departure']['time'];
                $scheduleData['arrival']['dateTime'] = $searchQueryDepartureDate." ".$scheduleData['arrival']['time'];
            }

            if (isset($scheduleData['arrival']['dateAdjustment'])) {
                $daysToBeAdded = $daysToBeAdded + $scheduleData['arrival']['dateAdjustment'];
                $scheduleData['arrival']['dateTime'] = date("Y-m-d", strtotime("+".$daysToBeAdded." day", strtotime($searchQueryDepartureDate)))." ".$scheduleData['arrival']['time'];
            }

            // extra field
            $scheduleData['step'] = $key; // to understand oneway/roundtrip/multicity

            $segmentArray[] = $scheduleData;
        }
    }

    // price related calculation
    $airlineInfo = DB::table('airlines')
                    // ->where('iata', $data['pricingInformation'][0]['fare']['validatingCarrierCode'])
                    ->where('iata', $segmentArray[0]['carrier']['operating'])
                    ->where('active', 'Y')
                    ->first();

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
@endphp

<div class="row flight_card">
    @if(count($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions']) == 1)
        @include('flight.result_row_v2_oneway')
    @else
        @include('flight.result_row_v2_roundtrip')
    @endif
</div>
