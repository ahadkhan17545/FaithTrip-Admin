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
                        <h5 class="alert-heading mb-0">SMS Gateway</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-xl-4">
                            <div class="card" style="@if($gateways[2]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8">KhudeBarta</div>
                                            <div class="col-lg-4 text-end">
                                                <input type="checkbox" class="switchery_checkbox" id="khudebarta" value="khudebarta" @if($gateways[2]->status == 1) checked @endif onchange="changeGatewayStatus('khudebarta')" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>
                
                                    <div class="row" style="height: 120px;">
                                        <div class="col-lg-12 text-center pt-4 pb-4">
                                            <img src="{{url('images')}}/khudebarta.jpg" style="max-width: 200px; max-height: 65px">
                                        </div>
                                    </div>
                
                                    <form class="needs-validation" method="POST" action="{{url('update/sms/gateway/info')}}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="provider" value="khudebarta">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="api_endpoint">API Endpoint</label>
                                                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{$gateways[2]->api_endpoint}}" class="form-control" placeholder="https://">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_endpoint')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="api_key">API Key</label>
                                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[2]->api_key}}" class="form-control" placeholder="ex. 7345876UYTUYR856778">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="secret_key">Secret Key</label>
                                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[2]->secret_key}}" class="form-control" placeholder="ex. 7345876UYTUYR8568">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('secret_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sender_id">Sender ID</label>
                                                    <input type="text" id="sender_id" name="sender_id" value="{{$gateways[2]->sender_id}}" class="form-control" placeholder="ex. Getup">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('sender_id')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="form-group text-center pt-3">
                                            <button class="btn btn-primary" type="submit">Update Info</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                
                        <div class="col-lg-4 col-xl-4">
                            <div class="card" style="@if($gateways[1]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8">Reve SMS</div>
                                            <div class="col-lg-4 text-end">
                                                <input type="checkbox" class="switchery_checkbox" id="revesms" value="revesms" @if($gateways[1]->status == 1) checked @endif onchange="changeGatewayStatus('revesms')" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>
                
                                    <div class="row" style="height: 120px;">
                                        <div class="col-lg-12 text-center pt-4 pb-4">
                                            <img src="{{url('images')}}/revesms.png" style="max-width: 200px; max-height: 130px">
                                        </div>
                                    </div>
                
                                    <form class="needs-validation" method="POST" action="{{url('update/sms/gateway/info')}}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="provider" value="revesms">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="api_endpoint">API Endpoint</label>
                                                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{$gateways[1]->api_endpoint}}" class="form-control" placeholder="https://">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_endpoint')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="api_key">API Key</label>
                                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[1]->api_key}}" class="form-control" placeholder="ex. 7345876UYTUYR856778">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="secret_key">Secret Key</label>
                                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[1]->secret_key}}" class="form-control" placeholder="ex. 7345876UYTUYR8568">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('secret_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sender_id">Sender ID</label>
                                                    <input type="text" id="sender_id" name="sender_id" value="{{$gateways[1]->sender_id}}" class="form-control" placeholder="ex. Getup">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('sender_id')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="form-group text-center pt-3">
                                            <button class="btn btn-primary" type="submit">Update Info</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                
                        <div class="col-lg-4 col-xl-4">
                            <div class="card" style="@if($gateways[0]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                                <div class="card-body">
                
                                    <h4 class="card-title mb-3">
                                        <div class="row">
                                            <div class="col-lg-8">ElitBuzz</div>
                                            <div class="col-lg-4 text-end">
                                                <input type="checkbox" class="switchery_checkbox" id="elitbuzz" @if($gateways[0]->status == 1) checked @endif value="elitbuzz" onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                                            </div>
                                        </div>
                                    </h4>
                
                                    <div class="row" style="height: 120px;">
                                        <div class="col-lg-12 text-center pt-4 pb-4">
                                            <img src="{{url('images')}}/elitebuzz.png" style="max-width: 200px; max-height: 130px">
                                        </div>
                                    </div>
                
                                    <form class="needs-validation" method="POST" action="{{url('update/sms/gateway/info')}}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="provider" value="elitbuzz">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="api_endpoint">API Endpoint</label>
                                                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{$gateways[0]->api_endpoint}}" class="form-control" placeholder="https://">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_endpoint')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="api_key">API Key</label>
                                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[0]->api_key}}" class="form-control" placeholder="ex. 7345876UYTUYR856778">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('api_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="secret_key">Secret Key</label>
                                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[0]->secret_key}}" class="form-control" placeholder="Don't Need This for ElitBuzz" readonly>
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('secret_key')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sender_id">Sender ID</label>
                                                    <input type="text" id="sender_id" name="sender_id" value="{{$gateways[0]->sender_id}}" class="form-control" placeholder="ex. Getup">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('sender_id')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="form-group text-center pt-3">
                                            <button class="btn btn-primary" type="submit">Update Info</button>
                                        </div>
                                    </form>
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
                url: "{{ url('change/gateway/status') }}"+'/'+provider,
                success: function (data) {
                    toastr.success("Status Changed", "Updated Successfully");
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

