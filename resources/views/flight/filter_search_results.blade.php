<div id="side-content" class="side-content rounded bg-white">

    <div class="mb-3 d-flex justify-content-between align-items-end">
        <h4 class="fs-16 mb-0">Price Range</h4>
        <a href="{{url('clear/price/range/filter')}}" class="reset fs-13 font-weight-500" style="font-weight: 600">Clear Range Filter</a>
    </div>

    <div class="row">
        <div class="col-lg-6" style="padding-right: 5px">
            <input type="number" id="filter_min_price" value="{{session('filter_min_price')}}" class="form-control" style="font-size: 12px;font-weight:600;color: rgb(236 98 47);" placeholder="Min Price">
        </div>
        <div class="col-lg-6" style="padding-left: 5px">
            <input type="number" id="filter_max_price" value="{{session('filter_max_price')}}" class="form-control" style="font-size: 12px;font-weight:600;color: rgb(236 98 47);" placeholder="Max Price">
        </div>
        <div class="col-lg-12 pt-2">
            <button type="button" onclick="priceRangeFilter()" class="btn btn-sm btn-success d-block rounded w-100">Filter Price</button>
        </div>
    </div>

    <hr>

    <div class="mb-3 d-flex justify-content-between align-items-end">
        <h4 class="fs-16 mb-0">Airline Carriers</h4>
        <a href="javascript:void(0)" class="reset fs-13 font-weight-500" style="font-weight: 600">Clear Carrier Filter</a>
    </div>
    @if (count($search_results_operating_carriers) > 0)
        @foreach ($search_results_operating_carriers as $carrierCode)
            @php
                $carrierCodeInfo = DB::table('airlines')->where('iata', $carrierCode)->first();
            @endphp
            @if ($carrierCodeInfo)
                <div class="custom-control custom-checkbox fs-14 d-flex check-area">
                    <input id="airline-{{ $carrierCode }}" type="checkbox" value="{{ $carrierCode }}" class="custom-control-input px-2 single-check">
                    <label class="custom-control-label px-2  d-flex align-items-center" for="airline-{{ $carrierCode }}">
                        <span class="checkbox-img d-flex align-items-center position-relative overflow-hidden">
                            <img src="{{ url('airlines_logo') }}/{{ $carrierCode }}.png">
                        </span>
                        <span class="airlines-name p-2" style="width: 190px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $carrierCodeInfo->name }}
                        </span>
                    </label>
                </div>
            @endif
        @endforeach
    @endif

</div>
