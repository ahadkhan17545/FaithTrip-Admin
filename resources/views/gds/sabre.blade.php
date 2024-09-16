@extends('master')

@section('header_css')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        .sabre_gds_info label{
            font-weight: 600;
        }

        .sabre_gds_info small{
            color: gray;
            font-size: 12px;
            padding-left: 3px;
        }
    </style>
@endsection


@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <img src="{{url($sabreGdsInfo->logo)}}" style="height: 18px;">
                </div>
                <div class="card-body sabre_gds_info">

                    <form action="{{url('update/sabre/gds/info')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="user_id">User ID (Sandbox)</label>
                                    <input type="text" id="user_id" name="user_id" value="{{$sabreGdsInfo->user_id}}" class="form-control" placeholder="V1:user:group:domain (V1:hxp6cy145bjv3hy7:DEVCENTER:EXT)">
                                    <small>This will be converted into Base64 during API Interaction</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="password">Password (Sandbox)</label>
                                    <input type="text" id="password" name="password" value="{{$sabreGdsInfo->password}}" class="form-control" placeholder="Hp1tT2iM">
                                    <small>This will be converted into Base64  during API Interaction</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="production_user_id">User ID (Production)</label>
                                    <input type="text" id="production_user_id" name="production_user_id" value="{{$sabreGdsInfo->production_user_id}}" class="form-control" placeholder="V1:user:group:domain (V1:hxp6cy145bjv3hy7:DEVCENTER:EXT)">
                                    <small>This will be converted into Base64 during API Interaction</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="production_password">Password (Production)</label>
                                    <input type="text" id="production_password" name="production_password" value="{{$sabreGdsInfo->production_password}}" class="form-control" placeholder="Hp1tT2iM">
                                    <small>This will be converted into Base64  during API Interaction</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mt-3 mb-2">
                                <div class="form-group">
                                    <label for="is_production" style="font-size: 16px; color: #d00000; cursor: pointer;">
                                        <input type="checkbox" name="is_production" @if($sabreGdsInfo->is_production == 1) checked @endif value="1" id="is_production">
                                        Enable Production Mode
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 pt-3">
                                <div class="form-group">
                                    <label for="description">Notes for Development Purpose</label>
                                    <textarea id="description" name="description" class="form-control">{!! $sabreGdsInfo->description !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 pt-3">
                                <button type="submit" class="btn btn-success rounded">Save Info</button>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')

    @if ($errors->any())
        <script>
            toastr.success("Sabre Gds Info Updated");
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#description').summernote({
            placeholder: 'Type Here',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
@endsection
