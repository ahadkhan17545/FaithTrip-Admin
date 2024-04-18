<div class="list-body col-md-7">
    <div class="row">
        <div class="col-12">
            <h6 class="list-hidden mb-1 fs-13 font-weight-500 text-primary">Round Trip</h6>
        </div>
    </div>

    @php
        $beginAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['beginAirport'];
        $beginAirportInfo = DB::table('city_airports')->where('airport_code', $beginAirportCode)->first();
        $endAirportCode = $data['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['endAirport'];
        $endAirportInfo = DB::table('city_airports')->where('airport_code', $endAirportCode)->first();
    @endphp

    <div class="row">
        <div class="col-3">
            <p class="mb-0 fs-14 font-weight-bold">{{$beginAirportInfo->city_name}}, {{$beginAirportInfo->country_name}} ({{$beginAirportInfo->city_code}})</p>
            <p class="mb-0 fs-12">21:25:00+06:00</p>
        </div>
        <div class="col-6 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">NonStop</span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-3 text-right">
            <p class="mb-0 fs-14 font-weight-bold">Dubai, United Arab Emirates (DXB)</p>
            <p class="mb-0 fs-12">01:00:00+04:00</p>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-3">
            <p class="mb-0 fs-14 font-weight-bold">Dhaka, Bangladesh (DAC)</p>
            <p class="mb-0 fs-12">21:25:00+06:00</p>
        </div>
        <div class="col-6 text-center">
            <div class="two-dots m-2 text-muted position-relative border-top">
                <span class="flight-service">
                    <span class="type-text px-2 position-relative">NonStop</span>
                </span>
            </div>
            <span class="mb-0 text-muted"></span>
        </div>
        <div class="col-3 text-right">
            <p class="mb-0 fs-14 font-weight-bold">Dubai, United Arab Emirates (DXB)</p>
            <p class="mb-0 fs-12">01:00:00+04:00</p>
        </div>
    </div>

</div>
