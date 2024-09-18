<div class="card-body">
    <h3 class="fs-17 mb-0">Fare summary</h3>
    <p class="fs-14">Travellers :
        @if(session('adult') > 0) {{session('adult')}} ADT @endif
        @if(session('child') > 0)&nbsp; {{session('child')}} CHD @endif
        @if(session('infant') > 0)&nbsp; {{session('infant')}} INF @endif
    </p>
    <hr>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="summary-text">
            <div class="font-weight-500">Base Fare</div>
        </div>
        <div class="fs-16 font-weight-500" style="font-weight: 600;">
            ({{$revalidatedData['currency']}}) {{number_format($revalidatedData['base_fare_amount'], 2)}}
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="summary-text">
            <div class="font-weight-500"> Total Tax Amount </div>
        </div>
        <div class="fs-16 font-weight-500" style="font-weight: 600;">
            ({{$revalidatedData['currency']}}) {{number_format($revalidatedData['total_tax_amount'], 2)}}
        </div>
    </div>
    <hr>
    <div class="d-flex align-items-center justify-content-between">
        <div class="">
            <div class="fs-14 font-weight-300">Total Payable Amount</div>
        </div>
        <div class="fs-16 font-weight-500">
            <span class="ml-2 text-primary" style="font-weight: 600;">
                ({{$revalidatedData['currency']}}) {{number_format($revalidatedData['total_fare'], 2)}}
            </span>
        </div>
    </div>
</div>
