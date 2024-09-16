<div class="list-body col-md-7">

    <div class="row">
        <div class="col-12">
            <h6 class="list-hidden mb-1 fs-13 font-weight-500 text-primary">Round Trip</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <p class="mb-0 fs-13 font-weight-bold" style="font-weight: 600;">
                {{$data['departure_airport_name']}}, {{$data['departure_city_name']}}, {{$data['departure_country_name']}} ({{$data['departure_airport_code']}})
            </p>
            <p class="mb-0 fs-16">
                @php
                    $stringDepartureDateTime = new DateTime($data['departure_datetime']);
                    $formattedDepartureDateTime = $stringDepartureDateTime->format('h:i a, d-m-y');
                    echo $formattedDepartureDateTime;
                @endphp
            </p>
        </div>
        <div class="col-4 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">
                        @if($data['onward_stops'] == 1)
                            {{$data['onward_stops']}} Stop
                        @else
                            {{$data['onward_stops']}} Stops
                        @endif
                    </span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-4 text-right">
            <p class="mb-0 fs-13 font-weight-bold" style="font-weight: 600;">
                {{$data['arrival_airport_name']}}, {{$data['arrival_city_name']}}, {{$data['arrival_country_name']}} ({{$data['arrival_airport_code']}})
            </p>
            <p class="mb-0 fs-16">
                @php
                    $stringDepartureDateTime = new DateTime($data['arrival_datetime']);
                    $formattedDepartureDateTime = $stringDepartureDateTime->format('h:i a, d-m-y');
                    echo $formattedDepartureDateTime;
                @endphp
            </p>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-4">
            <p class="mb-0 fs-13 font-weight-bold" style="font-weight: 600;">
                {{$data['return_departure_airport_name']}}, {{$data['return_departure_city_name']}}, {{$data['return_departure_country_name']}} ({{$data['return_departure_airport_code']}})
            </p>
            <p class="mb-0 fs-16">
                @php
                    $stringDepartureDateTime = new DateTime($data['return_departure_datetime']);
                    $formattedDepartureDateTime = $stringDepartureDateTime->format('h:i a, d-m-y');
                    echo $formattedDepartureDateTime;
                @endphp
            </p>
        </div>
        <div class="col-4 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">
                        @if($data['return_stops'] == 1)
                            {{$data['return_stops']}} Stop
                        @else
                            {{$data['return_stops']}} Stops
                        @endif
                    </span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-4 text-right">
            <p class="mb-0 fs-13 font-weight-bold" style="font-weight: 600;">
                {{$data['return_arrival_airport_name']}}, {{$data['return_arrival_city_name']}}, {{$data['return_arrival_country_name']}} ({{$data['return_arrival_airport_code']}})
            </p>
            <p class="mb-0 fs-16">
                @php
                    $stringDepartureDateTime = new DateTime($data['return_arrival_datetime']);
                    $formattedDepartureDateTime = $stringDepartureDateTime->format('h:i a, d-m-y');
                    echo $formattedDepartureDateTime;
                @endphp
            </p>
        </div>
    </div>

</div>
