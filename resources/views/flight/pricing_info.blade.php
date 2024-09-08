<div class="card-body">
    <h3 class="fs-17 mb-0">Fare summary</h3>
    <p class="fs-14">Travellers :
        @foreach ($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
            <b>
                {{ $passengerData['passengerInfo']['passengerNumber'] }}
                {{ $passengerData['passengerInfo']['passengerType'] }}
            </b>&nbsp;
        @endforeach
    </p>
    <hr>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="summary-text">
            <div class="font-weight-500">Base Fare</div>
        </div>
        <div class="fs-16 font-weight-500" style="font-weight: 600;">
            ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['currency'] }})

            @if (
                $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1][
                    'fare'
                ]['totalFare']['baseFareCurrency'] == 'USD')
                {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['baseFareAmount'] * $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['passengerInfoList'][0]['passengerInfo']['currencyConversion']['exchangeRateUsed']) }}
            @else
                {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['baseFareAmount']) }}
            @endif
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="summary-text">
            <div class="font-weight-500"> Total Tax Amount </div>
        </div>
        <div class="fs-16 font-weight-500" style="font-weight: 600;">
            ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['currency'] }})
            {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['totalTaxAmount']) }}
        </div>
    </div>
    <hr>
    <div class="d-flex align-items-center justify-content-between">
        <div class="">
            <div class="fs-14 font-weight-300">Total Payable Amount</div>
        </div>
        <div class="fs-16 font-weight-500">
            <span class="ml-2 text-primary" style="font-weight: 600;">
                ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['currency'] }})
                {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'])-1]['fare']['totalFare']['totalPrice']) }}
            </span>
        </div>
    </div>
</div>
