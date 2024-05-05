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
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading mb-0">Setup GDS</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="box">
                                <div class="row">
                                    <div class="col-lg-2" style="padding-right: 0px;">
                                        <div class="gds_logo">
                                            <img src="{{ url('/') }}/gds_logo/amadeus.png">
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <h5 style="margin-bottom: 5px">Amadeus GDS API</h5>
                                        <p class="mb-0" style="font-size: 12px">To configure or setup credentials click on settings</p>
                                    </div>
                                </div>
                                <hr style="background: #084277;">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="javascript:void(0)" data-id="amadeus" class="settings_btn"><i class="fas fa-cog"></i> Settings</a>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <label id="amadeus"><b>Status:</b></label> 
                                        <input type="checkbox" class="switchery_checkbox" id="amadeus" checked="" value="amadeus" onchange="changeGatewayStatus(this.value)" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="box">
                                <div class="row">
                                    <div class="col-lg-2" style="padding-right: 0px;">
                                        <div class="gds_logo">
                                            <img src="{{ url('/') }}/gds_logo/sabre.jpg">
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <h5 style="margin-bottom: 5px">Sabre GDS API</h5>
                                        <p class="mb-0" style="font-size: 12px">To configure or setup credentials click on settings</p>
                                    </div>
                                </div>
                                <hr style="background: #084277;">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="javascript:void(0)" data-id="amadeus" class="settings_btn"><i class="fas fa-cog"></i> Settings</a>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <label id="amadeus"><b>Status:</b></label> 
                                        <input type="checkbox" class="switchery_checkbox" id="amadeus" value="amadeus" onchange="changeGatewayStatus(this.value)" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554">
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
    </script>
@endsection