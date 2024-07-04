@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('update/mfs/account')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="slug" value="{{$data->slug}}">
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Update MFS Account</h5>
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
                                            <option value="1" @if($data->account_type == 1) selected @endif>bKash</option>
                                            <option value="2" @if($data->account_type == 2) selected @endif>Nagad</option>
                                            <option value="3" @if($data->account_type == 3) selected @endif>Rocket</option>
                                            <option value="4" @if($data->account_type == 4) selected @endif>Upay</option>
                                            <option value="5" @if($data->account_type == 5) selected @endif>Sure Cash</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="acc_no" class="col-form-label fw-bold justify-content-start d-flex">Account No <i class="text-danger">*</i></label>
                                        <input type="text" name="acc_no" value="{{$data->acc_no}}" id="acc_no" class="form-control" placeholder="01*********" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="status" class="col-form-label fw-bold justify-content-start d-flex">Status <i class="text-danger">*</i></label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option value="">Select One</option>
                                            <option value="1" @if($data->status == 1) selected @endif>Active</option>
                                            <option value="0" @if($data->status == 0) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Update MFS Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
