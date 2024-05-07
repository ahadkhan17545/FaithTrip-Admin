@extends('master')

@section('header_css')
    <link href="{{ url('assets') }}/admin-assets/css/switchery.min.css" rel="stylesheet" />
    <style>
        .box{
            border: 1px #084277;
            border-radius: 4px;
            padding: 15px;
            border-style: solid;
        }

        .box a.settings_btn{
            display: inline-block;
            background: #084277;
            padding: 4px 12px;
            border-radius: 4px;
            color: white;
            font-size: 15px;
            text-shadow: 1px 1px 3px black;
        }

        .gds_logo {
            display: block;
            width: 100%;
            position: relative;
            height: 50px; /* Adjust this height as needed */
        }

        .gds_logo img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection


@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-success mb-0" role="alert">
                        <h5 class="alert-heading mb-0">Setup GDS</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        @foreach ($gds as $item)
                        <div class="col-lg-6 mb-3">
                            <div class="box">
                                <div class="row">
                                    <div class="col-lg-2" style="padding-right: 0px;">
                                        <div class="gds_logo">
                                            <img src="{{url($item->logo)}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <h5 style="margin-bottom: 5px">{{$item->name}}</h5>
                                        <p class="mb-0" style="font-size: 12px">{{$item->description}}</p>
                                    </div>
                                </div>
                                <hr style="background: #084277;">
                                <div class="row">
                                    <div class="col-lg-6">

                                        @if($item->code == 'amadeus')
                                        <a href="javascript:void(0)" onclick="gdsSetupNotice()" class="settings_btn"><i class="fas fa-cog"></i> Settings</a>
                                        @endif

                                        @if($item->code == 'sabre')
                                        <a href="{{url('edit/gds')}}/{{$item->code}}" class="settings_btn"><i class="fas fa-cog"></i> Settings</a>
                                        @endif
                                
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <label for="{{$item->code}}"><b>Status:</b></label> 
                                        <input type="checkbox" id="{{$item->code}}" class="switchery_checkbox" @if($item->status == 1) checked="" @endif value="{{$item->code}}" onchange="changeGdsStatus(this.value)" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function gdsSetupNotice(){
            toastr.error("Amadeus is not Configured Yet, Contact with Developer");
            return false;
        }

        function changeGdsStatus(gds_code){

            var formData = new FormData();
            formData.append("gds_code", gds_code);

            if ($('#'+gds_code).prop('checked')) {
                formData.append("gds_status", 1);
            } else {
                formData.append("gds_status", 0);
            }

            $.ajax({
                data: formData,
                url: "{{ url('gds/status/update') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    
                    if ($('#'+gds_code).prop('checked')) {
                        toastr.success("Gds is Activated");
                    } else {
                        toastr.error("Gds is Inactivated");
                    }

                },
                error: function (data) {
                    toastr.error("Someting Went Wrong! Please Try Again");
                }
            });


        }
    </script>
@endsection