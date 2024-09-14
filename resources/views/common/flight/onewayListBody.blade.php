<div class="list-body col-md-7">
    <div class="d-none d-md-block">
        <h6 class="list-hidden mb-1 fs-13 font-weight-bold text-primary">One Way Trip</h6>
        <h6 class="align-items-center d-flex flex-wrap mb-2" style="font-size: 14px; line-height: 20px; font-weight: 600">
            <span>{{$data['departure_airport_name']}}, {{$data['departure_city_name']}}, {{$data['departure_country_name']}} ({{$data['departure_airport_code']}})</span>
            <svg class="bi bi-arrow-right  mx-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z"></path>
                <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z"></path>
            </svg>
            <span>
                {{$data['arrival_airport_name']}},
                {{$data['arrival_city_name']}},
                {{$data['arrival_country_name']}}
                ({{$data['arrival_airport_code']}})
            </span>
        </h6>
    </div>
    <div class="mb-2 d-none d-md-flex row">
        <div class="d-flex align-items-center flight-icon col">
            <div class="fli-content">
                <div class="fli_title fs-14 mb-1 font-weight-600"><i class="fas fa-plane-departure"></i> Departure</div>
                <div class="fli-text fs-16">
                    @php
                        $stringDepartureDateTime = new DateTime($data['departure_datetime']);
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
                        $stringArrivalDateTime = new DateTime($data['arrival_datetime']);
                        $formattedArrivalDateTime = $stringArrivalDateTime->format('h:i a, jS M-Y');
                        echo $formattedArrivalDateTime;
                    @endphp
                </div>
            </div>
        </div>
    </div>
    <div class="d-none d-md-flex align-items-sm-center text-center text-sm-left fs-14">
        <div class="fli-duration">
            <strong class="mr-1"><i class="far fa-clock"></i>
                @php
                    $time = App\Models\CustomFunction::convertIsoDurationToHoursAndMinutes($data['total_elapsed_time']);
                    echo $time['hours'] . " Hour " . $time['minutes'] . " mins";
                @endphp
            </strong>
        </div>
    </div>
</div>
