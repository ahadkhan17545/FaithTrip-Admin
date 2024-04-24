<div id="side-content" class="side-content rounded bg-white">
    <div>
        <div class="mb-3 d-flex justify-content-between align-items-end">
            <h4 class="fs-16 mb-0">Airline Operating Carriers</h4>
            <a href="javascript:void(0)" onclick="clearAirlineFilter()"
                class="reset fs-12 font-weight-500">Clear</a>
        </div>
        <div class="custom-control custom-checkbox fs-14 d-flex">
            <input id="airline-select-all" type="checkbox" name="select-all"
                class="custom-control-input ">
            <label class="custom-control-label px-2 d-flex align-items-center"
                for="airline-select-all">Select all</label>
        </div>

        @if (count($search_results_operating_carriers) > 0)
            @foreach ($search_results_operating_carriers as $carrierCode)
                @php
                    $carrierCodeInfo = DB::table('airlines')
                        ->where('iata', $carrierCode)
                        ->first();
                @endphp
                @if ($carrierCodeInfo)
                    <div class="custom-control custom-checkbox fs-14 d-flex check-area">
                        <input id="airline-{{ $carrierCode }}" type="checkbox"
                            name="carrier_code" value="{{ $carrierCode }}"
                            class="custom-control-input px-2 single-check">
                        <label class="custom-control-label px-2  d-flex align-items-center"
                            for="airline-{{ $carrierCode }}">
                            <span
                                class="checkbox-img d-flex align-items-center position-relative overflow-hidden">
                                <img
                                    src="{{ url('airlines_logo') }}/{{ $carrierCode }}.png">
                            </span>
                            <span class="airlines-name p-2"
                                style="width: 195px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $carrierCodeInfo->name }}
                            </span>
                        </label>
                    </div>
                @endif
            @endforeach
        @endif

    </div>

    <hr>

    <div>
        <div class="mb-3 d-flex justify-content-between align-items-end">
            <h4 class="fs-16 mb-0">Price Range</h4>
            <a href="#" onclick="clearTakeOff()"
                class="reset fs-12 font-weight-500">Clear</a>
        </div>
        <div class="row">
            <div class="col-lg-6" style="padding-right: 5px">
                <input type="number" name="min" class="form-control"
                    style="font-size: 12px;" placeholder="Min Price">
            </div>
            <div class="col-lg-6" style="padding-left: 5px">
                <input type="number" name="max" class="form-control"
                    style="font-size: 12px;" placeholder="Max Price">
            </div>
            <div class="col-lg-12 pt-2">
                <button type="button"
                    class="btn btn-sm btn-success d-block rounded w-100">Filter Price</button>
            </div>
        </div>
    </div>
</div>
