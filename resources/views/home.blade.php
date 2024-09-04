@extends('master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-selection {
            position: relative !important;
            box-shadow: none !important;
            border-radius: 20px !important;
        }

        .select2-selection__rendered {
            position: absolute !important;
            top: 42px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 225px;
            padding-left: 20px !important;
        }

        ul#select2-oneway_preferred_airlines-container{
            padding-left: 0px !important;
            display: contents;
        }
        ul#select2-roundtrip_preferred_airlines-container{
            padding-left: 0px !important;
            display: contents;
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

        .form-floating > label{
            font-size: 16px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height: 24px !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered{
            display: block;
            margin: 0;
            top: 38px !important;
            left: 15px !important;
        }

        .select2-container--default .select2-selection--multiple{
            background: transparent !important;
            padding-left: 15px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            font-size: 14px;
            padding: 0 3px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__display{
            padding-left: 2px;
            padding-right: 4px;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            margin-left: 4px;
            margin-top: 0px;
        }
        .select2-container--default .select2-search--inline .select2-search__field{
            padding-left: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="search_box_container">
        <img class="search_bg" src="{{ url('assets') }}/img/bg_search.jpg" alt="" />
        <div data-airport-url="#">
            <div class="mx-auto text-center top_part">
                <h2 class="top_heading">
                    <strong>Start your journey</strong> By one click
                    <span class="text-warning">Explore beautiful world!</span>
                </h2>
            </div>
            <div class="search-box container p-2">
                <div class="tab-content position-relative">
                    <div class="search-tabs d-flex flex-wrap">

                        <input class="checkbox d-none" id="tab2" type="radio" name="tabsA" checked />
                        <label class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white"
                            for="tab2">One way</label>
                        <input class="checkbox d-none" id="tab1" type="radio" name="tabsA" />
                        <label class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white"
                            for="tab1">Round trip</label>

                        <div class="search-content d-none w-100 pt-3" id="search-content2">
                            <form id="one_way_search_btn" class="modify-search">
                                <input type="hidden" name="oneway" value="1" />
                                <div class="search-row row no-gutters position-relative mx-0 mb-4">
                                    <div class="col-lg-5 px-0">
                                        <div class="input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="oneway_from">From</label>
                                                <select class="form-control border-bottom-0 border-right oneway_from" id="oneway_from" name="oneway_from"></select>
                                            </div>
                                            <span class="input-group-text">
                                                <svg class="bi bi-arrow-left-right" id="oneway-swap" width="1.2em"
                                                    height="1.2em" viewBox="0 0 16 16" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M10.146 7.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 11l-2.647-2.646a.5.5 0 0 1 0-.708z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 11a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 11zm3.854-9.354a.5.5 0 0 1 0 .708L3.207 5l2.647 2.646a.5.5 0 1 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2.5 5a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z">
                                                    </path>
                                                </svg>
                                            </span>
                                            <div class="form-floating flight-to">
                                                <label for="floatingInput">To</label>
                                                <select class="form-control border-bottom-0 border-right oneway_to" id="oneway_to" name="oneway_to"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 px-0 position-static">
                                        <div data-t-start data-t-end
                                            class="oneWay-datepicker t-datepicker t-datepicker-modal-oneway d-flex w-100 border-0 h-100"
                                            id="oneWayDatePicker">
                                            <div class="t-check-in"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 px-0">
                                        <div class="dropdown travellers-dropdown" id="dropdown-oneway">
                                            <div class="form-floating" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-haspopup="true">
                                                <input type="text" class="form-control dropdown-toggle"
                                                    id="passengers-oneway" value="1 Travelers, Economy" readonly />
                                                <label for="passengers">Traveler(s) cabin</label>
                                            </div>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="tab-container">
                                                    <div class="triangle abs"></div>
                                                    <ul class="m-0 p-0">
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="oneway-adult-input"
                                                                    class="all-input" readonly value="1" />
                                                                <span
                                                                    class="fs-16 font-weight-500">Adult<span>s</span></span>
                                                            </span>
                                                            <div class="spinner d-flex">
                                                                <span id="oneway-adult-minus" class="minus">-</span>
                                                                <span id="oneway-adult-plus" class="plus">+</span>
                                                            </div>
                                                            <input hidden name="adult_members" id="adult_input_one"
                                                                value="1" />
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="oneway-child-input"
                                                                    class="all-input" readonly value="0" />
                                                                <span class="fs-16 font-weight-500">Child</span>
                                                                <span class="cat-info fs-13">
                                                                    2 11 years</span>
                                                            </span>
                                                            <input hidden name="child_members" id="child_input_one"
                                                                value="0" />
                                                            <div class="spinner d-flex">
                                                                <span id="oneway-child-minus" class="minus"
                                                                    onclick="oneWayChildDec()">-</span>
                                                                <span id="oneway-child-plus" class="plus"
                                                                    onclick="oneWayChildInc()">+</span>
                                                            </div>
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <div data-child-total="0" class="_child_age_"
                                                                id="_child_age_"></div>
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="oneway-infant-input"
                                                                    class="all-input" readonly value="0" />
                                                                <span class="fs-16 font-weight-500">Infant</span>
                                                                <span class="cat-info fs-13">Below 2 years</span>
                                                            </span>
                                                            <div class="spinner d-flex">
                                                                <span id="oneway-infant-minus" class="minus">-</span>
                                                                <span id="oneway-infant-plus" class="plus">+</span>
                                                            </div>
                                                            <input hidden name="infant_members" id="infant_input_one"
                                                                value="0" />
                                                        </li>
                                                    </ul>
                                                    <div class="class-type mt-2">
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="economy1" name="classType1"
                                                                class="custom-control-input economy1" checked />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="economy1">Economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="premiumEconomy1" name="classType1"
                                                                class="custom-control-input premiumEconomy1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="premiumEconomy1">Premium economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="first1" name="classType1"
                                                                class="custom-control-input first1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="first1">First</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="business1" name="classType1"
                                                                class="custom-control-input business1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="business1">Business</label>
                                                        </div>
                                                    </div>
                                                    <input hidden name="classType" id="class_type_one" value="Y" />
                                                    <div class="cat-sel mt-3 text-right">
                                                        <input type="button" class="btn btn-danger w-100"
                                                            onclick="oneWayTotalPassenger()" value="Confirm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 px-0">
                                        <div class="input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="oneway_preferred_airlines">Preferred Airlines</label>
                                                <select class="form-control border-bottom-0 border-right oneway_preferred_airlines" id="oneway_preferred_airlines" name="oneway_preferred_airlines[]" multiple>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="btn-hub-oneway">
                                    <button type="button" style="padding: 0.8rem 2rem;" onclick="searchForFlights(1)" id="btn-search-oneway" class="btn btn-primary btn-search">
                                        Search flights
                                        <i class="fas fa-plane-departure"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="search-content d-none w-100 pt-3" id="search-content1">
                            <form class="modify-search">
                                <input type="hidden" name="round" value="1" />
                                <div class="search-row row no-gutters position-relative mx-0 mb-4">
                                    <div class="col-lg-5 px-0">
                                        <div class="input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="floatingInput">From</label>
                                                <select class="form-control border-bottom-0 border-right round_trip_from"
                                                    id="round_trip_from" name="round_trip_from"></select>
                                            </div>
                                            <span class="input-group-text">
                                                <svg class="bi bi-arrow-left-right" id="round-swap" width="1.2em"
                                                    height="1.2em" viewBox="0 0 16 16" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M10.146 7.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 11l-2.647-2.646a.5.5 0 0 1 0-.708z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 11a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 11zm3.854-9.354a.5.5 0 0 1 0 .708L3.207 5l2.647 2.646a.5.5 0 1 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2.5 5a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z">
                                                    </path>
                                                </svg>
                                            </span>
                                            <div class="form-floating flight-to">
                                                <label for="floatingInput">To</label>
                                                <select class="form-control border-bottom-0 border-right round_trip_to"
                                                    id="round_trip_to" name="round_trip_to"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 px-0 position-static">
                                        <div data-t-start data-t-end
                                            class="oneWay-datepicker t-datepicker t-datepicker-modal-round d-flex w-100 border-0"
                                            id="roundDatePicker">
                                            <div class="t-check-in w-100"></div>
                                            <div class="t-check-out w-100"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 px-0">
                                        <div class="dropdown travellers-dropdown" id="dropdown-roundTrip">
                                            <div class="form-floating" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                aria-haspopup="true">
                                                <input type="text" class="form-control dropdown-toggle"
                                                    id="passengers-roundTrip" value="1 Travellers, Economy" />
                                                <label for="passengers">Traveller(s) cabin</label>
                                            </div>
                                            <div class="dropdown-menu dropdown-menu-right drop-down-round"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="tab-container">
                                                    <div class="triangle abs"></div>
                                                    <ul class="m-0 p-0">
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="round-adult-input"
                                                                    class="all-input" readonly value="1" />
                                                                <span
                                                                    class="fs-16 font-weight-500">Adult<span>S</span></span>
                                                            </span>
                                                            <div class="spinner d-flex">
                                                                <span id="round-adult-minus" class="minus">-</span>
                                                                <span id="round-adult-plus" class="plus">+</span>
                                                            </div>
                                                            <input hidden name="adult_members" id="adult_input"
                                                                value="1" />
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="round-child-input"
                                                                    class="all-input" readonly value="0" />
                                                                <span class="fs-16 font-weight-500">Child</span>
                                                                <span class="cat-info fs-13">2 11 years</span>
                                                            </span>
                                                            <input hidden name="child_members" id="child_input"
                                                                value="0" />
                                                            <div class="spinner d-flex">
                                                                <span id="round-child-minus" class="minus"
                                                                    onclick="roundChildDec()">-</span>
                                                                <span id="round-child-plus" class="plus"
                                                                    onclick="roundChildInc()">+</span>
                                                            </div>
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <div data-child-total="0" class="_child_age_"
                                                                id="_child_age_"></div>
                                                        </li>
                                                        <li class="noOf d-flex justify-content-between">
                                                            <span>
                                                                <input type="text" id="round-infant-input"
                                                                    class="all-input" readonly value="0" />
                                                                <span class="fs-16 font-weight-500">Infant</span>
                                                                <span class="cat-info fs-13">Below 2 years</span>
                                                            </span>
                                                            <input hidden name="infant_members" id="infant_input"
                                                                value="0" />
                                                            <div class="spinner d-flex">
                                                                <span id="round-infant-minus" class="minus">-</span>
                                                                <span id="round-infant-plus" class="plus">+</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <div class="class-type mt-2">
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="economy2" name="classType1"
                                                                class="custom-control-input economy1" checked />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="economy1">Economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="premiumEconomy2" name="classType1"
                                                                class="custom-control-input premiumEconomy1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="premiumEconomy1">Premium economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="first2" name="classType1"
                                                                class="custom-control-input first1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="first1">First</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="business2" name="classType1"
                                                                class="custom-control-input business1" />
                                                            <label class="custom-control-label fs-16 font-weight-500"
                                                                for="business1">Business</label>
                                                        </div>
                                                    </div>
                                                    <input hidden name="classType" id="class_type" value="Y" />
                                                    <div class="cat-sel mt-3 text-right">
                                                        <input type="button" class="btn btn-danger w-100"
                                                            onclick="roundTripTotalPassenger()" value="Confirm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 px-0">
                                        <div class="input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="roundtrip_preferred_airlines">Preferred Airlines</label>
                                                <select class="form-control border-bottom-0 border-right roundtrip_preferred_airlines" id="roundtrip_preferred_airlines" name="roundtrip_preferred_airlines[]" multiple>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="btn-hub-round">
                                    <button type="button" onclick="searchForFlights(2)" id="btn-search-round"
                                        class="btn btn-primary btn-search">
                                        Search flights
                                        <i class="fas fa-plane-departure"></i>
                                        </a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/module-assets/js/booking/search_box.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>

        $('.oneway_from').select2({
            placeholder: 'Departure City/Airport',
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
            placeholder: 'Departure City/Airport',
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
            placeholder: 'Destination City/Airport',
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
            placeholder: 'Destination City/Airport',
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

        $('.oneway_preferred_airlines').select2({
            placeholder: 'Preferred Airlines',
            minimumInputLength: 2,
            ajax: {
                url: '/live/airline/search',
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

        $('.roundtrip_preferred_airlines').select2({
            placeholder: 'Preferred Airlines',
            minimumInputLength: 2,
            ajax: {
                url: '/live/airline/search',
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
                var preferred_airlines = $("#oneway_preferred_airlines").val();
            } else {
                var departureLocationId = $("#round_trip_from").val();
                var destinationLocationId = $("#round_trip_to").val();
                var departureDate = document.querySelector('#roundDatePicker .t-check-in input[name="t-start"]').value;
                returnDate = document.querySelector('#roundDatePicker .t-check-out input[name="t-end"]').value;
                var adult = Number($("#round-adult-input").val());
                var child = Number($("#round-child-input").val());
                var infant = Number($("#round-infant-input").val());
                var preferred_airlines = $("#roundtrip_preferred_airlines").val();
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
            formData.append("preferred_airlines", preferred_airlines);

            $.ajax({
                data: formData,
                url: "{{ url('search/flights') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".page-loader-wrapper").hide();
                    window.location.href = "/flight/search-results";
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
