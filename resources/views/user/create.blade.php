@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('save/b2b/user')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Create B2B User Account</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <h5>Account Info</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name" class="col-form-label fw-bold justify-content-start d-flex">Full Name <i class="text-danger">*</i></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Mr./Miss./Mrs." required="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="col-form-label fw-bold justify-content-start d-flex">Email (Username for Login) <i class="text-danger">*</i></label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="sample@email.com" required="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="col-form-label fw-bold justify-content-start d-flex">Phone No <i class="text-danger">*</i></label>
                                        <input type="text" name="phone" id="phone" class="form-control" placeholder="01*********" required="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="col-form-label fw-bold justify-content-start d-flex">Password <i class="text-danger">*</i></label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="********" required="">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="comission" class="col-form-label fw-bold justify-content-start d-flex">Profit Comission (In Percentage)</label>
                                        <input type="number" name="comission" id="comission" class="form-control" max="7" placeholder="%">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="nid" class="col-form-label fw-bold justify-content-start d-flex">NID</label>
                                        <input type="text" name="nid" id="nid" class="form-control" placeholder="55874589654">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="image" class="col-form-label fw-bold justify-content-start d-flex">Profile Image</label>
                                        <input type="file" name="image" id="image" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5>Company Info</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="company_name" class="col-form-label fw-bold justify-content-start d-flex">Company Name <i class="text-danger">*</i></label>
                                        <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Ltd." required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_email" class="col-form-label fw-bold justify-content-start d-flex">Company Email</label>
                                        <input type="email" name="company_email" id="company_email" class="form-control" placeholder="company@email.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_phone" class="col-form-label fw-bold justify-content-start d-flex">Phone No</label>
                                        <input type="text" name="company_phone" id="company_phone" class="form-control" placeholder="01*********">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tin" class="col-form-label fw-bold justify-content-start d-flex">TIN</label>
                                        <input type="text" name="tin" id="tin" class="form-control" placeholder="Tax Identification No">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bin" class="col-form-label fw-bold justify-content-start d-flex">BIN</label>
                                        <input type="text" name="bin" id="bin" class="form-control" placeholder="Business Identification No (BIN)">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="company_address" class="col-form-label fw-bold justify-content-start d-flex">Company Address</label>
                                        <input type="text" name="company_address" id="company_address" class="form-control" placeholder="Address">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="brand_logo" class="col-form-label fw-bold justify-content-start d-flex">Brand Logo</label>
                                        <input type="file" name="brand_logo" id="brand_logo" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-5">
                            <div class="col-lg-12">
                                <h5>User Access</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="status" class="col-form-label fw-bold" style="padding-left: 5px; cursor: pointer;">
                                            <input type="checkbox" name="status" value="1" id="status" style="margin-right: 5px" checked>
                                            Account Access
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="search_status" class="col-form-label fw-bold" style="padding-left: 5px; cursor: pointer;">
                                            <input type="checkbox" name="search_status" value="1" id="search_status" style="margin-right: 5px" checked>
                                            Flight Search
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="booking_status" class="col-form-label fw-bold" style="padding-left: 5px; cursor: pointer;">
                                            <input type="checkbox" name="booking_status" value="1" id="booking_status" style="margin-right: 5px" checked>
                                            Flight Booking
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="ticket_status" class="col-form-label fw-bold" style="padding-left: 5px; cursor: pointer;">
                                            <input type="checkbox" name="ticket_status" value="1" id="ticket_status" style="margin-right: 5px" checked>
                                            Ticket Issue
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Create Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
