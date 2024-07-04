@extends('master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection{
            min-height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <form action="{{url('submit/recharge/request')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="alert alert-success mb-0" role="alert">
                            <h5 class="alert-heading mb-0">Submit Topup Request</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">

                                <div class="row pb-3">
                                    <div class="co-lg-12">
                                        <div class="form-group">
                                            <label for="payment_method" class="col-form-label">Payment Method<span class="text-danger">*</span></label>
                                            <select id="payment_method" name="payment_method" onchange="paymentMethod()" class="form-control" required>
                                                <option value="">Select One</option>
                                                <option value="1">Bank Transfer</option>
                                                <option value="2">Bank Cheque</option>
                                                <option value="3">bKash</option>
                                                <option value="4">Nagad</option>
                                                <option value="5">Rocket</option>
                                                <option value="6">Upay</option>
                                                <option value="7">Sure Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-none" id="bank_account">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="admin_bank_account_id" class="col-form-label">Sent To<span class="text-danger">*</span></label>
                                            <select class="form-select" name="admin_bank_account_id" id="admin_bank_account_id">
                                                <option value="">Select One</option>
                                                @foreach ($bankAccounts as $bankAccount)
                                                <option value="{{$bankAccount->id}}">{{$bankAccount->acc_no}} - {{$bankAccount->bank_name}} ({{$bankAccount->branch_name."-".$bankAccount->routing_no}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="bank_name" class="col-form-label">Bank Name<span class="text-danger">*</span></label>
                                            <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="Bank Name"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="branch_name" class="col-form-label">Branch Name<span class="text-danger">*</span></label>
                                            <input type="text" id="branch_name" name="branch_name" class="form-control" placeholder="Branch Name"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="routing_no" class="col-form-label">Routing No</label>
                                            <input type="text" id="routing_no" name="routing_no" class="form-control" placeholder="Routing No"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="acc_holder_name" class="col-form-label">Account Holder Name<span class="text-danger">*</span></label>
                                            <input type="text" id="acc_holder_name" name="acc_holder_name" class="form-control" placeholder="Account Holder Name"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="acc_no" class="col-form-label">Account No<span class="text-danger">*</span></label>
                                            <input type="text" id="acc_no" name="acc_no" class="form-control" placeholder="Account No"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-none" id="bank_cheque">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="cheque_no" class="col-form-label">Cheque No<span class="text-danger">*</span></label>
                                            <input type="text" id="cheque_no" name="cheque_no" class="form-control" placeholder="Cheque No"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="cheque_bank_name" class="col-form-label">Cheque Bank<span class="text-danger">*</span></label>
                                            <input type="text" id="cheque_bank_name" name="cheque_bank_name" class="form-control" placeholder="Cheque Bank Name"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="deposite_date" class="col-form-label">Deposite Date<span class="text-danger">*</span></label>
                                            <input type="date" id="deposite_date" name="deposite_date" class="form-control" placeholder="Deposite Date"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-none" id="mobile_account">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="admin_mfs_account_id" class="col-form-label">Sent To<span class="text-danger">*</span></label>
                                            <select class="form-select" name="admin_mfs_account_id" id="admin_mfs_account_id">
                                                <option value="">Select One</option>
                                                @foreach ($mfsAccounts as $mfsAccount)
                                                    @php
                                                        $mfsAccountType = '';
                                                        if($mfsAccount->account_type == 1)
                                                            $mfsAccountType = 'bKash';
                                                        if($mfsAccount->account_type == 2)
                                                            $mfsAccountType = 'Nagad';
                                                        if($mfsAccount->account_type == 3)
                                                            $mfsAccountType = 'Rocket';
                                                        if($mfsAccount->account_type == 4)
                                                            $mfsAccountType = 'Upay';
                                                        if($mfsAccount->account_type == 5)
                                                            $mfsAccountType = 'SureCash';
                                                    @endphp
                                                    <option value="{{$mfsAccount->id}}" class="{{$mfsAccountType}}">{{$mfsAccount->acc_no}} ({{$mfsAccountType}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile_no" class="col-form-label">Mobile Account No<span class="text-danger">*</span></label>
                                            <input type="text" id="mobile_no" name="mobile_no" class="form-control" placeholder="Mobile Account No"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row pt-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="recharge_amount" class="col-form-label">Recharge Amount</label>
                                            <input type="text" id="recharge_amount" name="recharge_amount" placeholder="BDT" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="transaction_id" class="col-form-label">Transaction ID</label>
                                            <input type="text" id="transaction_id" value="{{ old('transaction_id') }}" placeholder="Transaction ID" name="transaction_id" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="remarks" class="col-form-label">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Comments"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="attachment" class="col-form-label">Attachment</label>
                                    <input type="file" name="attachment" class="form-control">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Submit Topup Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer_js')
    <script>

        $( document ).ready(function() {
            paymentMethod();
        });

        function paymentMethod(){
            var paymentMethod = parseInt($("#payment_method").val());
            if(paymentMethod && paymentMethod > 0){
                if(paymentMethod == 1){
                    $("#bank_account").removeClass("d-none");
                    $("#bank_account").addClass("d-block");

                    $("#bank_cheque").removeClass("d-block");
                    $("#bank_cheque").addClass("d-none");

                    $("#mobile_account").removeClass("d-block");
                    $("#mobile_account").addClass("d-none");
                } else if(paymentMethod == 2){
                    $("#bank_cheque").removeClass("d-none");
                    $("#bank_cheque").addClass("d-block");

                    $("#mobile_account").removeClass("d-block");
                    $("#mobile_account").addClass("d-none");

                    $("#bank_account").removeClass("d-block");
                    $("#bank_account").addClass("d-none");
                } else {

                    $("option.bKash").css("display", "none")
                    $("option.Nagad").css("display", "none")
                    $("option.Rocket").css("display", "none")
                    $("option.Upay").css("display", "none")
                    $("option.SureCash").css("display", "none")

                    if(paymentMethod == '3'){
                        $("option.bKash").css("display", "block")
                    }
                    if(paymentMethod == '4'){
                        $("option.Nagad").css("display", "block")
                    }
                    if(paymentMethod == '5'){
                        $("option.Rocket").css("display", "block")
                    }
                    if(paymentMethod == '6'){
                        $("option.Upay").css("display", "block")
                    }
                    if(paymentMethod == '7'){
                        $("option.SureCash").css("display", "block")
                    }

                    $("#mobile_account").removeClass("d-none");
                    $("#mobile_account").addClass("d-block");

                    $("#bank_cheque").removeClass("d-block");
                    $("#bank_cheque").addClass("d-none");

                    $("#bank_account").removeClass("d-block");
                    $("#bank_account").addClass("d-none");
                }
            } else {
                $("#mobile_account").removeClass("d-block");
                $("#mobile_account").addClass("d-none");
                $("#bank_account").removeClass("d-block");
                $("#bank_account").addClass("d-none");
                $("#bank_cheque").removeClass("d-block");
                $("#bank_cheque").addClass("d-none");
            }
        }
    </script>
@endsection
