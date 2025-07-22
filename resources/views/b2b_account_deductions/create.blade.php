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
            <form action="{{url('deduct/b2b/account')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Submit B2B Account Deduction</h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">

                                <div class="form-group pb-2">
                                    <label for="b2b_user_id" class="col-form-label">Select B2B User<span class="text-danger">*</span></label>
                                    <select id="b2b_user_id" name="b2b_user_id" onchange="getCurrentBalance(this.value)" class="form-select" required>
                                        <option value="">Select One</option>
                                        @foreach ($b2bUsers as $b2bUser)
                                        <option value="{{$b2bUser->id}}">{{$b2bUser->name}} - {{$b2bUser->email}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group pb-2">
                                            <label for="current_balance" class="col-form-label">Current Balance</label>
                                            <input type="text" id="current_balance" name="current_balance" placeholder="BDT" class="form-control" readonly/>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group pb-2">
                                            <label for="amount" class="col-form-label">Amount to be Deducted<span class="text-danger">*</span></label>
                                            <input type="number" id="amount" name="amount" placeholder="BDT" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="details" class="col-form-label">Reason (Optional)</label>
                                    <textarea id="details" name="details" class="form-control" placeholder="Comments"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success">Deduct Now</button>
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
