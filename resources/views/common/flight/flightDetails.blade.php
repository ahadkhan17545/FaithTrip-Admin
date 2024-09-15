<div class="collapse" id="collapse1_{{ $index }}">
    <div class="bg-white fli-det card card-body rounded-0 border-0 p-0 pt-3 px-md-3 py-md-3">
        <ul class="nav nav-tabs border-bottom-0  mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded fs-14 font-weight-500 active" id="info-tab" data-bs-toggle="tab" href="#info_{{ $index }}" role="tab" aria-controls="info" aria-selected="true">Flight info</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded fs-14 font-weight-500" id="fare-getails-tab" data-bs-toggle="tab" href="#fare-getails_{{ $index }}" role="tab" aria-controls="home" aria-selected="true">Fare details</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded fs-14 font-weight-500"
                    id="baggage-tab" data-bs-toggle="tab"
                    href="#baggage_{{ $index }}" role="tab"
                    aria-controls="baggage" aria-selected="false">Baggage</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded fs-14 font-weight-500"
                    id="cancellation-tab" data-bs-toggle="tab"
                    href="#cancellation_{{ $index }}" role="tab"
                    aria-controls="cancellation"
                    aria-selected="false">Cancellation</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="info_{{ $index }}" role="tabpanel" aria-labelledby="info-tab">

                @foreach ($data['segments'] as $segmentIndex => $segmentData)
                    <div class="flight-info border rounded mt-1 mb-1">
                        <div class="flight-scroll review-article">
                            <div class="align-items-center d-flex custom-gap justify-content-between w-100">
                                <div class="align-items-center d-flex gap-4 text-center">
                                    <div class="brand-img">
                                        <img src="{{ url('airlines_logo') }}/{{ strtolower($segmentData['operating_carrier_code']) }}.png">
                                    </div>
                                    <div class="airline-box">
                                        <div class="font-weight-600 fs-13">
                                            {{ $segmentData['operating_carrier_code'] }}
                                        </div>
                                        <div class="font-weight-600 fs-13 text-muted w-max-content">
                                            {{ $segmentData['operating_flight_number'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="font-weight-600 fs-13">
                                        {{ $segmentData['departure_airport_code'] }}
                                    </div>
                                    <span class="fs-12 font-weight-600" style="width: 80px; display: inline-block;">
                                        @php
                                            $departureDateTime = new DateTime($segmentData['departure_datetime']);
                                            echo $departureDateTime->format('jS M-y, h:i a');
                                        @endphp
                                    </span><br>
                                    <span class="text-muted fs-12">
                                        Terminal -
                                        {{ isset($segmentData['departure_terminal']) ? $segmentData['departure_terminal'] : 'N/A' }}
                                    </span>
                                </div>
                                <div class="text-center">
                                    <div class="font-weight-600 fs-13">
                                        {{ $segmentData['arrival_airport_code'] }}
                                    </div>
                                    <span class="fs-12 font-weight-600" style="width: 80px; display: inline-block;">
                                        @php
                                            $arrivalDateTime = new DateTime($segmentData['arrival_datetime']);
                                            echo $arrivalDateTime->format('jS M-y, h:i a');
                                        @endphp
                                    </span><br>
                                    <span class="text-muted fs-12">
                                        Terminal -
                                        {{ isset($segmentData['arrival_terminal']) ? $segmentData['arrival_terminal'] : 'N/A' }}
                                    </span>
                                </div>
                                <div class="text-center fs-14 w-100">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="d-inline-flex align-items-center w-max-content">
                                            @php
                                                $time = App\Models\CustomFunction::convertIsoDurationToHoursAndMinutes($segmentData['elapsed_time']);
                                                echo $time['hours'] . " Hour " . $time['minutes'] . " mins";
                                            @endphp
                                        </span>
                                        <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                            Meal - @if($segmentData['meal_code']) {{$segmentData['meal_code']}} @else N/A @endif
                                        </span>
                                        <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                            Booking Code - @if($segmentData['booking_code']) {{$segmentData['booking_code']}} @else N/A @endif
                                        </span>
                                    </div>
                                    <div class="two-dots my-3 text-muted position-relative border-top">
                                        <span class="flight-service">
                                            <span class="type-text px-2 position-relative">Flight</span>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="d-inline-flex align-items-center w-max-content">
                                            @php
                                                if(isset($segmentData['baggage_allowance']['checked'])){
                                                    foreach ($segmentData['baggage_allowance']['checked'] as $baggageDataIndex => $baggageData) {
                                                        echo $baggageData['passenger_type'].': '.$baggageData['title']."&nbsp;&nbsp;";
                                                    }
                                                } else {
                                                    if(isset($segmentData['baggage_allowance']['carry_on'])){
                                                        foreach ($segmentData['baggage_allowance']['carry_on'] as $baggageDataIndex => $baggageData) {
                                                            echo $baggageData['passenger_type'].': '.$baggageData['title']."&nbsp;&nbsp;";
                                                        }
                                                    }
                                                }
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($data['segments'][$segmentIndex+1]))
                    <div class="d-flex justify-center px-3">
                        <span class="fs-12 layover text-center">
                            @php
                                $datetime1 = new DateTime($segmentData['arrival_datetime']);
                                $datetime2 = new DateTime($data['segments'][$segmentIndex+1]['departure_datetime']);
                                $interval = $datetime1->diff($datetime2);
                                $hours = $interval->h;
                                $minutes = $interval->i;
                                echo "{$hours}hr {$minutes}min";
                            @endphp
                        </span>
                    </div>
                    @endif
                @endforeach

            </div>
            <div class="tab-pane fade" id="fare-getails_{{ $index }}" role="tabpanel" aria-labelledby="fare-getails-tab">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm mb-0 text-center">
                            <tbody>
                                <tr>
                                    <td><b>Passenger Type</b></td>
                                    <td><b>Base fare</b></td>
                                    <td><b>Tax fare</b></td>
                                    <td><b>Total fare</b></td>
                                </tr>
                                <tr>
                                    <td>
                                        @if(session('adult') > 0)
                                            <strong>Adult: {{session('adult')}} &nbsp;</strong>
                                        @endif
                                        @if(session('child') > 0)
                                            <strong>Child: {{session('child')}} &nbsp;</strong>
                                        @endif
                                        @if(session('infant') > 0)
                                            <strong>Infant: {{session('infant')}}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($data['base_fare_amount'], 2) }}
                                        ({{ $data['currency'] }})
                                    </td>
                                    <td>
                                        {{ number_format($data['total_tax_amount'], 2) }}
                                        ({{ $data['currency'] }})
                                    </td>
                                    <td>
                                        {{ number_format($data['total_fare'], 2) }}
                                        ({{ $data['currency'] }})
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="baggage_{{ $index }}" role="tabpanel" aria-labelledby="baggage-tab">
                <div class="row">
                    <div class="col-md-6 pr-md-2">

                        <table class="table table-bordered table-sm mb-0 text-center">
                            <tbody>
                                <tr>
                                    <td>Passenger Type</td>
                                    <td>Weight</td>
                                    <td>Provision Type</td>
                                </tr>

                                @if(isset($segmentData['baggage_allowance']['checked']))
                                    @foreach ($segmentData['baggage_allowance']['checked'] as $baggageDataIndex => $baggageData)
                                        <tr>
                                            <td>{{ $baggageData['passenger_type'] }}</td>
                                            <td>{{ $baggageData['title'] }}</td>
                                            <td style="text-transform: capitalize;">{{ str_replace("_"," ",$baggageData['baggage_type']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if(isset($segmentData['baggage_allowance']['carry_on']))
                                    @foreach ($segmentData['baggage_allowance']['carry_on'] as $baggageDataIndex => $baggageData)
                                        <tr>
                                            <td>{{ $baggageData['passenger_type'] }}</td>
                                            <td>{{ $baggageData['title'] }}</td>
                                            <td style="text-transform: capitalize;">{{ str_replace("_"," ",$baggageData['baggage_type']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-6  pl-md-2">
                        <p class="mb-0">The baggage information is just for
                            reference please check with airline before check in
                            for more information visit airlines website</p>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="cancellation_{{ $index }}" role="tabpanel" aria-labelledby="cancellation-tab">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm text-center">
                            <thead>
                                <tr class="font-weight-bold">
                                    <td>Refundable</td>
                                    <td>Changeable before departure</td>
                                    <td>Penalty</td>
                                    <td>Penalty Applicable</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-transform: capitalize;">{{str_replace("-"," ",$data['refundable'])}}</td>
                                    <td style="text-transform: capitalize;">{{$data['change_before_departure']}}</td>
                                    <td>{{$data['penalty']}}</td>
                                    <td style="text-transform: capitalize;">{{$data['penalty_applicable']}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
