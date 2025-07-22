@extends('master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-container--default .select2-selection--single .select2-selection__arrow b::before{
            content: "" !important;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('update/banner')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Update Banner Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">

                                <input type="hidden" name="banner_id" value="{{$data->id}}">

                                <div class="form-group pb-1">
                                    <label for="image" class="col-form-label">Banner Image (480*240)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="image" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" accept=".png, .jpg, .jpeg ,.svg, .JPG, .PNG">
                                    <img id="blah" @if($data->image && file_exists(public_path($data->image))) src="{{url($data->image)}}" @endif alt="" class="img-fluid">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="url" class="col-form-label">Banner URL</label>
                                            <input type="text" id="url" name="url" value="{{$data->url}}" placeholder="https://" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="status" class="col-form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="1" @if($data->status == 1) selected @endif>Active</option>
                                                <option value="0" @if($data->status == 0) selected @endif>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Update Now</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('footer_js')
    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getCurrentBalance(user_id){
            $.ajax({
                type: "GET",
                url: "{{ url('get/user/balance') }}"+'/'+user_id,
                success: function (data) {
                    $("#current_balance").val(data.balance)
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.error("Something Went Wrong");
                }
            });
        }

    </script>
@endsection
