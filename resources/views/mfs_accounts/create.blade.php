@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('save/mfs/account')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Add MFS Account</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="account_type" class="col-form-label fw-bold justify-content-start d-flex">Account Type <i class="text-danger">*</i></label>
                                        <select class="form-select" name="account_type" id="account_type" required>
                                            <option value="">Select</option>
                                            <option value="1">bKash</option>
                                            <option value="2">Nagad</option>
                                            <option value="3">Rocket</option>
                                            <option value="4">Upay</option>
                                            <option value="5">Sure Cash</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="acc_no" class="col-form-label fw-bold justify-content-start d-flex">Account No <i class="text-danger">*</i></label>
                                        <input type="text" name="acc_no" id="acc_no" class="form-control" placeholder="01*********" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Save MFS Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
