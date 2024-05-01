@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('update/company/profile')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">Company Profile</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name" class="col-form-label fw-bold justify-content-start d-flex">Company Name <i class="text-danger">*</i></label>
                                        <input type="text" name="name" @if($companyProfile) value="{{$companyProfile->name}}" @endif id="name" class="form-control" placeholder="Company Name" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="email" class="col-form-label fw-bold justify-content-start d-flex">Company email <i class="text-danger">*</i></label>
                                        <input type="email" name="email" @if($companyProfile) value="{{$companyProfile->email}}" @endif id="email" class="form-control" placeholder="Email" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="phone" class="col-form-label fw-bold justify-content-start d-flex">Company phone <i class="text-danger">*</i></label>
                                        <input type="text" name="phone" @if($companyProfile) value="{{$companyProfile->email}}" @endif id="phone" class="form-control" placeholder="Phone Number" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="logo" class="col-form-label fw-bold justify-content-start d-flex">Brand Logo</label>
                                        <input type="file" name="logo" id="logo" class="form-control">

                                        @if($companyProfile && $companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                                            <img class="max-h-45 mt-3 mb-2" src="{{url($companyProfile->logo)}}" /><br>
                                            <a href="{{url('remove/company/logo')}}">❌ Remove Logo</a>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="tin" class="col-form-label fw-bold justify-content-start d-flex">TIN No</label>
                                        <input type="text" name="tin" id="tin" class="form-control" placeholder="TIN No" @if($companyProfile) value="{{$companyProfile->tin}}" @endif>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="bin" class="col-form-label fw-bold justify-content-start d-flex">BIN No</label>
                                        <input type="text" name="bin" id="bin" class="form-control" placeholder="BIN No" @if($companyProfile) value="{{$companyProfile->bin}}" @endif>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address" class="col-form-label fw-bold justify-content-start d-flex">Address <i class="text-danger">*</i></label>
                                        <textarea name="address" rows="4" id="address" class="form-control" placeholder="Address">@if($companyProfile){{$companyProfile->address}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer_js')
    @if(isset($successMsg))
        <script>
            toastr.success("Company Profile Updated");
        </script>
    @endif
@endsection
