@extends('master')

@section('header_css')
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
                                    <label for="user_id">User ID</label>
                                    <input type="text" id="user_id" name="user_id" value="{{$sabreGdsInfo->user_id}}" class="form-control" placeholder="V1:user:group:domain (V1:hxp6cy145bjv3hy7:DEVCENTER:EXT)">
                                    <small>This will be converted into Base64 during API Interaction</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" id="password" name="password" value="{{$sabreGdsInfo->password}}" class="form-control" placeholder="Hp1tT2iM">
                                    <small>This will be converted into Base64  during API Interaction</small>
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

    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description', {
            filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
            filebrowserUploadMethod: 'form',
            height: 300,
        });
    </script>
@endsection