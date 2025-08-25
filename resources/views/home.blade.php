@extends('master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/css/homepage.css" rel="stylesheet" />
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

                        <label class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white">
                            <input type="radio" name="flight_type" value="1" onclick="showOnewayDate()" checked> One
                            way
                        </label>
                        <label class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white">
                            <input type="radio" name="flight_type" value="2" onclick="showRoundTripDate()"> Round
                            trip
                        </label>
                        <label class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white">
                            <input type="radio" name="flight_type" value="3" onclick="showMultiCityDate()"> Multi
                            City
                        </label>

                        <div class="search-content d-block w-100 pt-3" id="search-content2">
                            <form class="modify-search">
                                <input type="hidden" id="flight_type" value="1">
                                <div class="search-row row no-gutters position-relative mx-0 mb-4">
                                    <div class="col-lg-5 px-0">
                                        <div class="input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="flight_from">From</label>
                                                <select class="form-control border-bottom-0 border-right flight_from"
                                                    id="flight_from"></select>
                                            </div>
                                            <span class="input-group-text">
                                                <img src="{{ url('assets') }}/admin-assets/img/arrow-symbol.png"
                                                    id="oneway-swap">
                                            </span>
                                            <div class="form-floating flight-to">
                                                <label for="flight_to">To</label>
                                                <select class="form-control border-bottom-0 border-right flight_to"
                                                    id="flight_to"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 px-0 position-static">
                                        <div data-t-start data-t-end
                                            class="oneWay-datepicker t-datepicker t-datepicker-modal-oneway d-flex w-100 border-0 h-100 d-block"
                                            id="oneWayDatePicker">
                                            <div class="t-check-in"></div>
                                        </div>

                                        <div data-t-start data-t-end
                                            class="oneWay-datepicker t-datepicker t-datepicker-modal-round d-flex w-100 border-0 d-none"
                                            id="roundDatePicker">
                                            <div class="t-check-in w-100"></div>
                                            <div class="t-check-out w-100"></div>
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
                                                                <span class="cat-info fs-13">2 11 years</span>
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
                                                            <input type="radio" id="economy1" name="cabin_class_oneway" value="economy" class="cabin_class_oneway custom-control-input economy1" checked />
                                                            <label class="custom-control-label fs-16 font-weight-500" for="economy1">Economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="premiumEconomy1" name="cabin_class_oneway" value="premium_economy" class="cabin_class_oneway custom-control-input premiumEconomy1" />
                                                            <label class="custom-control-label fs-16 font-weight-500" for="premiumEconomy1">Premium economy</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="business1" name="cabin_class_oneway" value="business" class="cabin_class_oneway custom-control-input business1" />
                                                            <label class="custom-control-label fs-16 font-weight-500" for="business1">Business</label>
                                                        </div>
                                                        <div class="custom-control custom-radio pl-0">
                                                            <input type="radio" id="first1" name="cabin_class_oneway" value="first_class" class="cabin_class_oneway custom-control-input first1" />
                                                            <label class="custom-control-label fs-16 font-weight-500" for="first1">First-Class</label>
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
                                    <div class="col-lg-2 last-col px-0">
                                        <div class="input-group custom-input-group rounded">
                                            <div class="form-floating flight-form">
                                                <label for="preferred_airlines">Preferred Airlines</label>
                                                <select
                                                    class="form-control border-bottom-0 border-right preferred_airlines"
                                                    id="preferred_airlines" name="preferred_airlines[]" multiple>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <button type="button" id="add_another_city"
                                            class="btn btn-primary multicity-btn d-none">
                                            <i class="far fa-plus-square"></i> Add Another City
                                        </button>
                                    </div>
                                </div>

                                <div id="btn-hub-oneway">
                                    <button type="button" style="padding: 0.8rem 2rem;" onclick="searchForFlights()"
                                        id="btn-search-oneway" class="btn btn-primary btn-search">
                                        Search flights
                                        <i class="fas fa-plane-departure"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if (count($banners) > 0)
            @include('promotional_banners')
        @endif

    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/module-assets/js/booking/search_box.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ url('assets') }}/plugins/swiper/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".services-slider", {
            loop: true,
            slidesPerView: 2,
            spaceBetween: 16,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            // Responsive breakpoints
            breakpoints: {
                300: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 1,
                },
                992: {
                    slidesPerView: 2,
                },
                1200: {
                    slidesPerView: 2,
                }
            }
        });

        $('.flight_from').select2({
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

        $('.flight_to').select2({
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

        $('.preferred_airlines').select2({
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

        function createRemovableRow(fromDiv) {
            const clone = fromDiv.cloneNode(true);
            // clone.querySelectorAll("input, textarea, select").forEach(el => el.value = "");
            const lastCol = clone.querySelector(".last-col");
            if (lastCol) {
                const existingBtn = lastCol.querySelector(".remove-btn");
                if (!existingBtn) {
                    const removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "<i class='fas fa-times'></i>";
                    removeBtn.className = "search-row-remove btn";
                    removeBtn.style.marginLeft = "10px";
                    removeBtn.addEventListener("click", function() {
                        clone.remove();
                    });
                    lastCol.appendChild(removeBtn);
                }
            }
            return clone;
        }

        document.getElementById("add_another_city").addEventListener("click", function() {
            const original = document.querySelector(".search-row"); // the first one
            const newRow = createRemovableRow(original);
            const allRows = document.querySelectorAll(".search-row");
            const lastRow = allRows[allRows.length - 1];
            lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
        });


        function showOnewayDate() {
            $("#flight_type").val(1);

            // removing extra row of multicity search
            const allRows = document.querySelectorAll(".search-row");
            for (let i = 1; i < allRows.length; i++) {
                allRows[i].remove();
            }

            // multicity add city button
            var multicityBtn = document.querySelector('.multicity-btn');
            multicityBtn.classList.remove('d-inline-block');
            multicityBtn.classList.add('d-none');

            // hide roundtrip date
            var roundTripDiv = document.querySelector('.t-datepicker-modal-round');
            roundTripDiv.classList.remove('d-block');
            roundTripDiv.classList.add('d-none');

            // show oneway date
            var onewayDiv = document.querySelector('.t-datepicker-modal-oneway');
            onewayDiv.classList.remove('d-none');
            onewayDiv.classList.add('d-block');
        }

        function showRoundTripDate() {
            $("#flight_type").val(2);

            // removing extra row of multicity search
            const allRows = document.querySelectorAll(".search-row");
            for (let i = 1; i < allRows.length; i++) {
                allRows[i].remove();
            }

            // multicity add city button
            var multicityBtn = document.querySelector('.multicity-btn');
            multicityBtn.classList.remove('d-inline-block');
            multicityBtn.classList.add('d-none');

            // hide oneway date
            var onewayDiv = document.querySelector('.t-datepicker-modal-oneway');
            onewayDiv.classList.remove('d-block');
            onewayDiv.classList.add('d-none');

            // show roundtrip date
            var roundTripDiv = document.querySelector('.t-datepicker-modal-round');
            roundTripDiv.classList.remove('d-none');
            roundTripDiv.classList.add('d-block');
        }

        function showMultiCityDate() {
            $("#flight_type").val(3);

            // hide roundtrip date
            var roundTripDiv = document.querySelector('.t-datepicker-modal-round');
            roundTripDiv.classList.remove('d-block');
            roundTripDiv.classList.add('d-none');

            // show oneway date
            var onewayDiv = document.querySelector('.t-datepicker-modal-oneway');
            onewayDiv.classList.remove('d-none');
            onewayDiv.classList.add('d-block');

            // adding row for multicity search
            const original = document.querySelector(".search-row"); // the first one
            const newRow = createRemovableRow(original);
            const allRows = document.querySelectorAll(".search-row");
            const lastRow = allRows[allRows.length - 1];
            lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);

            // multicity add city button
            var multicityBtn = document.querySelector('.multicity-btn');
            multicityBtn.classList.remove('d-none');
            multicityBtn.classList.add('d-inline-block');
        }

        function searchForFlights() {

            var flightType = $("#flight_type").val(); // 1=>Oneway; 2=>Return
            let returnDate = '';

            var departureLocationId = $("#flight_from").val();
            var destinationLocationId = $("#flight_to").val();
            var preferred_airlines = $("#preferred_airlines").val();
            var adult = Number($("#oneway-adult-input").val());
            var child = Number($("#oneway-child-input").val());
            var infant = Number($("#oneway-infant-input").val());
            var cabinClass = $('input.cabin_class_oneway:checked').val();

            if (flightType == 1) {
                var departureDate = document.querySelector('#oneWayDatePicker .t-check-in input[name="t-start"]').value;
            } else {
                var departureDate = document.querySelector('#roundDatePicker .t-check-in input[name="t-start"]').value;
                returnDate = document.querySelector('#roundDatePicker .t-check-out input[name="t-end"]').value;
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
            formData.append("cabin_class", cabinClass);

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
