<div class="search_result">
    <div class="bg-white hox list-item mb-3 rounded position-relative demo NonStop">

        <div class="m-0 align-items-center row">

            <div class="list-item_start text-center col-md-2">
                <div class="d-flex d-md-block justify-content-center">
                    <div class="list-item_logo">

                        <img class="img-fluid" src="{{ url('airlines_logo') }}/{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}.png" alt="{{ strtolower($data['pricingInformation'][0]['fare']['validatingCarrierCode']) }}">
                        <small class="d-block mt-1">
                            @php
                                $airlineInfo = DB::table('airlines')
                                    ->where('iata', $data['pricingInformation'][0]['fare']['validatingCarrierCode'])
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


            <div class="list-item_end title hotel-right clearfix grid-hidden d-flex align-items-center d-md-block mt-2 pt-2 col-md-3 justify-content-center">
                <div class="price-area d-xl-flex align-items-xl-center justify-content-xl-center text-center">
                    <div class="purchase-price fs-24 font-weight-600">
                        <div class="main-price">
                            {{ $data['pricingInformation'][0]['fare']['totalFare']['currency'] }}
                            {{ $data['pricingInformation'][0]['fare']['totalFare']['totalPrice'] }}
                        </div>
                    </div>
                </div>
                <div class="d-flex d-md-block gap-4 ml-auto ms-3 mt-md-3 text-center">
                    <a href="#" id="BookNowBtn hox" class="btn btn-primary text-uppercase font-weight-600 fs-13 btn_filters_responsive disable_book_now_cls">Book now</a>
                    <a class="fli-det-link text-muted fs-14 gap-2 d-block mt-md-2 d-flex align-items-center justify-content-center mr-2 mr-md-0" data-bs-toggle="collapse" href="#collapse1_{{ $index }}" role="button" aria-expanded="false" aria-controls="collapse1">
                        Flight details
                        <i class="fas fa-chevron-circle-down ml-1 text-success"></i>
                    </a>
                </div>
            </div>
        </div>

        @include('flight.flight_details')

    </div>
</div>
