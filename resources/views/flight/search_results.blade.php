@extends('master')

@section('content')

    <div class="row">
        <div class="search-content-wrap m-auto">

            @if (isset($searchResults['groupedItineraryResponse']))
                <div class="sorting my-3">
                    <div class="badge bg-primary fs-16 mb-2 mb-lg-0">
                        Total
                        <span class="font-weight-500">
                            <b id="total_flights">{{ $searchResults['groupedItineraryResponse']['statistics']['itineraryCount'] }}</b>
                        </span>
                        Flights found
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
                                                                        ->first();
                                                                    $arrivalLocation = DB::table('city_airports')
                                                                        ->where('city_code', $data['arrivalLocation'])
                                                                        ->first();
                                                                @endphp
                                                                {{ $departureLocation->city_name }},
                                                                {{ $departureLocation->country_name }}
                                                                ({{ $departureLocation->city_code }})
                                                                <i class="fas fa-plane-departure"></i>
                                                                {{ $arrivalLocation->city_name }},
                                                                {{ $arrivalLocation->country_name }}
                                                                ({{ $arrivalLocation->city_code }}),
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

                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="border:none;">
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
                                    @endphp

                                    @if (($minPrice && $minPrice > 0) && (!$maxPrice && $maxPrice == 0))
                                        @if ($totalPrice >= $minPrice)
                                            @include('flight.result_row')
                                        @endif
                                    @elseif (($maxPrice && $maxPrice > 0) && (!$minPrice && $minPrice == 0))
                                        @if ($totalPrice <= $maxPrice)
                                            @include('flight.result_row')
                                        @endif
                                    @elseif (($minPrice && $minPrice > 0) && ($maxPrice && $maxPrice > 0))
                                        @if ($totalPrice >= $minPrice && $totalPrice <= $maxPrice)
                                            @include('flight.result_row')
                                        @endif
                                    @else
                                        @include('flight.result_row')
                                    @endif

                                @endforeach

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-none d-lg-block leftSidebar mb-3">
                        <div class="theiaStickySidebar">
                            @include('flight.filter_search_results')
                        </div>
                    </div>

                </div>
            @else
                {{-- if no flights found --}}
                <div class="sorting my-3">
                    <div class="badge bg-primary fs-16 mb-2 mb-lg-0 w-100 p-3 mt-4">
                        Sorry! No Flights found &nbsp;&nbsp;
                        <a href="{{ url('/') }}" class="d-inline btn btn-sm btn-rounded" style="background: #ffffffe8; font-weight: 600;">Search Again</a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('footer_js')
    <script>
        function priceRangeFilter(){

            var minPrice = Number($("#filter_min_price").val());
            var maxPrice = Number($("#filter_max_price").val());

            if(!minPrice && !maxPrice){
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
                    success: function (data) {
                        $(".page-loader-wrapper").hide();
                        // window.location.href = "/flight/search-results";
                        location.reload();
                    },
                    error: function (data) {
                        $(".page-loader-wrapper").hide();
                        toastr.error("Someting Went Wrong! Please Try Again");
                    }
                });
            }
        }
    </script>
@endsection
