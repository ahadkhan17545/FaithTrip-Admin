@extends('master')

@section('header_css')
    <link href="{{ url('assets') }}/admin-assets/css/switchery.min.css" rel="stylesheet" />
    <style>
        .form-group{
            margin-bottom: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-success mb-0" role="alert">
                        <h5 class="alert-heading mb-0">Search Results View</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-xl-6">
                            <div class="card" style="@if($config->search_results_view == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8">Simple View</div>
                                            <div class="col-lg-4 text-end">
                                                <input type="checkbox" class="switchery_checkbox" id="khudebarta" value="khudebarta" @if($config->search_results_view == 1) checked @endif onchange="changeGatewayStatus(1)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>

                                    <div class="row" style="height: 280px;">
                                        <div class="col-lg-12 text-center pt-4 pb-4">
                                            <img src="{{url('assets')}}/img/SimpleView.png" style="max-height: 120px">
                                            <p class="mt-2">
                                                The simple view presents a concise summary of flight details, highlighting essential information such as departure and arrival times, total flight duration, layovers with transit times, and the total cost. This compact layout focuses on quick comparisons and ease of selection, ideal for users seeking a straightforward overview without additional details.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-xl-6">
                            <div class="card" style="@if($config->search_results_view == 2) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8">Detailed View</div>
                                            <div class="col-lg-4 text-end">
                                                <input type="checkbox" class="switchery_checkbox" id="revesms" value="revesms" @if($config->search_results_view == 2) checked @endif onchange="changeGatewayStatus(2)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>

                                    <div class="row" style="height: 280px;">
                                        <div class="col-lg-12 text-center pt-4 pb-4">
                                            <img src="{{url('assets')}}/img/DetailedView.png" style="max-height: 120px">
                                            <p class="mt-2">
                                                The detailed view provides an in-depth breakdown of flight itineraries, including specific flight segments, layover durations, baggage allowances, meal information, and terminal details. It caters to users who want a comprehensive understanding of their journey, ensuring transparency and clarity for better-informed decisions.
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('footer_js')
    <script src="{{ url('assets') }}/admin-assets/js/switchery.min.js"></script>
    <script>
        $('[data-toggle="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });

        function changeGatewayStatus(value){
            var provider = value;
            $.ajax({
                type: "GET",
                url: "{{ url('change/search/results/view') }}"+'/'+provider,
                success: function (data) {
                    toastr.success("View Changed", "Updated Successfully");
                    setTimeout(function() {
                        console.log("Wait For 1 Sec");
                        location.reload(true);
                    }, 1000);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    </script>
@endsection

