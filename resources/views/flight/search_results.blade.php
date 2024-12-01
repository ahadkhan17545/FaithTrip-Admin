@extends('master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-selection {
            position: relative !important;
            box-shadow: none !important;
        }

        .select2-selection__rendered {
            position: absolute !important;
            top: 42px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 230px;
            padding-left: 20px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 20px !important;
        }

        .select2-container--open .select2-dropdown {
            top: 45px !important;
        }

        .select2-selection__arrow {
            display: none;
        }

        .select2-selection__placeholder {
            font-weight: 600;
        }

        .input-group {
            height: 76px
        }
        a.search_next, a.search_prev{
            background: #084277;
            color: white;
            text-decoration: none;
            padding: 2px 15px;
            margin: 0px;
            border-radius: 4px;
            font-weight: 600;
            text-shadow: 1px 1px 1px black;
            font-size: 15px;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="search-content-wrap m-auto">

            @if (isset($searchResults['groupedItineraryResponse']['itineraryGroups']))
                <div class="sorting my-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="badge bg-primary fs-16 mb-2 mb-lg-0">
                                Total
                                <span class="font-weight-500">
                                    <b id="total_flights">{{ $searchResults['groupedItineraryResponse']['statistics']['itineraryCount'] }}</b>
                                </span>
                                Flights found
                            </div>
                        </div>
                        <div class="col-lg-5 text-end">
                            @if(session('departure_date') > date("Y-m-d"))
                            <a href="{{url('search/prev/day')}}" onclick="showLoader()" class="d-inline-block search_prev"><i class="fas fa-arrow-left"></i> Previous Day</a>
                            @endif
                            <a href="{{url('search/next/day')}}" onclick="showLoader()" class="d-inline-block search_next">Next Day <i class="fas fa-arrow-right"></i></a>
                        </div>
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
                                                                        ->orWhere(
                                                                            'airport_code',
                                                                            $data['departureLocation'],
                                                                        )
                                                                        ->first();
                                                                    $arrivalLocation = DB::table('city_airports')
                                                                        ->where('city_code', $data['arrivalLocation'])
                                                                        ->orWhere(
                                                                            'airport_code',
                                                                            $data['arrivalLocation'],
                                                                        )
                                                                        ->first();
                                                                @endphp

                                                                {{ $departureLocation->city_name }},
                                                                {{ $departureLocation->country_name }}
                                                                ({{ $departureLocation->city_code }})
                                                                <i class="fas fa-plane-departure"></i>
                                                                {{ $arrivalLocation ? $arrivalLocation->city_name : '' }},
                                                                {{ $arrivalLocation ? $arrivalLocation->country_name : '' }}
                                                                ({{ $arrivalLocation ? $arrivalLocation->city_code : '' }}),
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
                                    @php
                                        $totalPrice = $data['pricingInformation'][0]['fare']['totalFare']['totalPrice'];
                                        $minPrice = session('filter_min_price');
                                        $maxPrice = session('filter_max_price');
                                        $airlineCarrierFilterArray = session('airline_carrier_code');
                                    @endphp

                                    @if ($minPrice && $minPrice > 0 && (!$maxPrice && $maxPrice == 0))
                                        @if ($totalPrice >= $minPrice)
                                            @if (session('airline_carrier_code') &&
                                                    in_array($data['pricingInformation'][0]['fare']['validatingCarrierCode'], session('airline_carrier_code')))
                                                @include('flight.result_row')
                                            @endif

                                            @if (!session('airline_carrier_code'))
                                                @include('flight.result_row')
                                            @endif
                                        @endif
                                    @elseif ($maxPrice && $maxPrice > 0 && (!$minPrice && $minPrice == 0))
                                        @if ($totalPrice <= $maxPrice)
                                            @if (session('airline_carrier_code') &&
                                                    in_array($data['pricingInformation'][0]['fare']['validatingCarrierCode'], session('airline_carrier_code')))
                                                @include('flight.result_row')
                                            @endif

                                            @if (!session('airline_carrier_code'))
                                                @include('flight.result_row')
                                            @endif
                                        @endif
                                    @elseif ($minPrice && $minPrice > 0 && ($maxPrice && $maxPrice > 0))
                                        @if ($totalPrice >= $minPrice && $totalPrice <= $maxPrice)
                                            @if (session('airline_carrier_code') &&
                                                    in_array($data['pricingInformation'][0]['fare']['validatingCarrierCode'], session('airline_carrier_code')))
                                                @include('flight.result_row')
                                            @endif

                                            @if (!session('airline_carrier_code'))
                                                @include('flight.result_row')
                                            @endif
                                        @endif
                                    @else
                                        @if (session('airline_carrier_code') &&
                                                in_array($data['pricingInformation'][0]['fare']['validatingCarrierCode'], session('airline_carrier_code')))
                                            @include('flight.result_row')
                                        @endif

                                        @if (!session('airline_carrier_code'))
                                            @include('flight.result_row')
                                        @endif
                                    @endif
                                @endforeach

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-lg-block leftSidebar mb-3">
                        <div class="theiaStickySidebar">
                            @include('flight.search_session_time')
                            @include('flight.filter_search_results')
                        </div>
                    </div>

                </div>
            @else
                {{-- if no flights found --}}
                <div class="sorting my-3">
                    <div class="badge bg-primary fs-16 mb-2 mb-lg-0 w-100 p-3">
                        Sorry! No Flights found &nbsp;&nbsp;
                        <a href="{{ url('/') }}" class="d-inline btn btn-sm btn-rounded"
                            style="background: #ffffffe8; font-weight: 600;">Search Again</a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/module-assets/js/booking/search_box.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>

        function showLoader(){
            $(".page-loader-wrapper").show();
        }

        $('.oneway_from').select2({
            placeholder: 'Search Departure City/Airport',
            minimumInputLength: 2,
            ajax: {
                url: '/live/city/airport/search',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.search_result,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $('.oneway_to').select2({
            placeholder: 'Search Destination City/Airport',
            minimumInputLength: 2,
            ajax: {
                url: '/live/city/airport/search',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.search_result,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $('.round_trip_from').select2({
            placeholder: 'Search Depart. City/Airport',
            minimumInputLength: 2,
            ajax: {
                url: '/live/city/airport/search',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.search_result,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $('.round_trip_to').select2({
            placeholder: 'Search Dest. City/Airport',
            minimumInputLength: 2,
            ajax: {
                url: '/live/city/airport/search',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.search_result,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        function priceRangeFilter() {

            var minPrice = Number($("#filter_min_price").val());
            var maxPrice = Number($("#filter_max_price").val());

            if (!minPrice && !maxPrice) {
                toastr.error("Please provide Min or Max range");
                return false;
            } else {

                $(".page-loader-wrapper").show();

                var formData = new FormData();
                formData.append("min_price", minPrice);
                formData.append("max_price", maxPrice);

                $.ajax({
                    data: formData,
                    url: "{{ url('price/range/filter') }}",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $(".page-loader-wrapper").hide();
                        // window.location.href = "/flight/search-results";
                        location.reload();
                    },
                    error: function(data) {
                        $(".page-loader-wrapper").hide();
                        toastr.error("Someting Went Wrong! Please Try Again");
                    }
                });
            }
        }

        function airlineCarrierFilter(carrierCode) {
            if (!carrierCode || carrierCode == '' || carrierCode == null) {
                toastr.error("Airline Carrier Code is Null");
                return false;
            } else {

                $(".page-loader-wrapper").show();

                var formData = new FormData();
                formData.append("airline_carrier_code", carrierCode);
                if ($("#airline-" + carrierCode).is(":checked")) {
                    formData.append("type", 'add');
                } else {
                    formData.append("type", 'remove');
                }

                $.ajax({
                    data: formData,
                    url: "{{ url('airline/carrier/filter') }}",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $(".page-loader-wrapper").hide();
                        location.reload();
                    },
                    error: function(data) {
                        $(".page-loader-wrapper").hide();
                        toastr.error("Someting Went Wrong! Please Try Again");
                    }
                });
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function searchForFlights(flightType) {

            var flightType = flightType; // 1=>Oneway; 2=>Return
            let returnDate = '';

            if (flightType == 1) {
                var departureLocationId = $("#oneway_from").val();
                var destinationLocationId = $("#oneway_to").val();
                var departureDate = document.querySelector('#oneWayDatePicker .t-check-in input[name="t-start"]').value;
                var adult = Number($("#oneway-adult-input").val());
                var child = Number($("#oneway-child-input").val());
                var infant = Number($("#oneway-infant-input").val());
            } else {
                var departureLocationId = $("#round_trip_from").val();
                var destinationLocationId = $("#round_trip_to").val();
                var departureDate = document.querySelector('#roundDatePicker .t-check-in input[name="t-start"]').value;
                returnDate = document.querySelector('#roundDatePicker .t-check-out input[name="t-end"]').value;
                var adult = Number($("#round-adult-input").val());
                var child = Number($("#round-child-input").val());
                var infant = Number($("#round-infant-input").val());
            }


            if (!departureLocationId) {
                toastr.error("Departure Location is missing");
                return false;
            }
            if (!destinationLocationId) {
                toastr.error("Destination Location is missing");
                return false;
            }
            if (departureDate == '') {
                toastr.error("Departure Date is missing");
                return false;
            }
            if (flightType == 2 && returnDate == '') {
                toastr.error("Return Date is mendatory for Round Trip");
                return false;
            }
            if ((adult + child + infant) <= 0) {
                toastr.error("Please Provide Passanger Information");
                return false;
            }

            if (departureLocationId == destinationLocationId) {
                toastr.error("Departure & Destination Cannot be Same");
                return false;
            }


            $(".page-loader-wrapper").show();

            var formData = new FormData();
            formData.append("flight_type", flightType);
            formData.append("departure_location_id", departureLocationId);
            formData.append("destination_location_id", destinationLocationId);
            formData.append("departure_date", departureDate);
            formData.append("return_date", returnDate);
            formData.append("adult", adult);
            formData.append("child", child);
            formData.append("infant", infant);

            $.ajax({
                data: formData,
                url: "{{ url('search/flights') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".page-loader-wrapper").hide();
                    // window.location.href = "/flight/search-results";
                    location.reload();
                },
                error: function(data) {
                    // console.log('Error:', data);
                    $(".page-loader-wrapper").hide();
                    toastr.error("Someting Went Wrong! Please Try Again");
                }
            });

        }
    </script>
@endsection
