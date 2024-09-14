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
                    <img src="{{url($flyhubGdsInfo->logo)}}" style="height: 25px;">
                </div>
                <div class="card-body sabre_gds_info">

                    <form action="{{url('update/flyhub/gds/info')}}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$flyhubGdsInfo->api_key}}" class="form-control" placeholder="S22841596461569276">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="password">Secret Code</label>
                                    <input type="text" id="secret_code" name="secret_code" value="{{$flyhubGdsInfo->secret_code}}" class="form-control" placeholder="F5ZxtnzhOKkvK7Ukc8nT2ahh3PBfV2R">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mt-3 mb-2">
                                <div class="form-group">
                                    <label for="is_production" style="font-size: 16px; color: #d00000; cursor: pointer;">
                                        <input type="checkbox" name="is_production" @if($flyhubGdsInfo->is_production == 1) checked @endif value="1" id="is_production">
                                        Enable Production Mode
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 pt-3">
                                <div class="form-group">
                                    <label for="description">Notes for Development Purpose</label>
                                    <textarea id="description" name="description" class="form-control">{!! $flyhubGdsInfo->description !!}</textarea>
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
            toastr.success("Flyhub Gds Info Updated");
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
