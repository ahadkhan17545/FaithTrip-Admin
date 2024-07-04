@extends('master')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('update/bank/account')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="slug" value="{{$data->slug}}">
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Update Bank Account</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="bank_name" class="col-form-label fw-bold justify-content-start d-flex">Bank Name <i class="text-danger">*</i></label>
                                        <input type="text" name="bank_name" value="{{$data->bank_name}}" id="bank_name" class="form-control" placeholder="Bank Name" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="branch_name" class="col-form-label fw-bold justify-content-start d-flex">Branch Name <i class="text-danger">*</i></label>
                                        <input type="text" name="branch_name" value="{{$data->branch_name}}" id="branch_name" class="form-control" placeholder="Branch Name" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="routing_no" class="col-form-label fw-bold justify-content-start d-flex">Routing No <i class="text-danger">*</i></label>
                                        <input type="text" name="routing_no" value="{{$data->routing_no}}" id="routing_no" class="form-control" placeholder="Routing No" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="swift_code" class="col-form-label fw-bold justify-content-start d-flex">Swift Code</label>
                                        <input type="text" name="swift_code" value="{{$data->swift_code}}" id="swift_code" class="form-control" placeholder="Swift Code">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="acc_holder_name" class="col-form-label fw-bold justify-content-start d-flex">ACC. Holder Name <i class="text-danger">*</i></label>
                                        <input type="text" name="acc_holder_name" value="{{$data->acc_holder_name}}" id="acc_holder_name" class="form-control" placeholder="ACC. Holder Name" required="">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="acc_no" class="col-form-label fw-bold justify-content-start d-flex">Account No <i class="text-danger">*</i></label>
                                        <input type="text" name="acc_no" id="acc_no" value="{{$data->acc_no}}" class="form-control" placeholder="ACC No." required="">
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
                        <button type="submit" class="btn btn-success">Update Bank Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
