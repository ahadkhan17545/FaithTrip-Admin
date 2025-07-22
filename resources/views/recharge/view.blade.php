@extends('master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td:nth-child(1) {
            font-weight: 600;
        }

        table.dataTable tbody td:nth-child(13) {
            min-width: 100px;
        }

        table.dataTable tbody {
            text-align: center !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        table#DataTables_Table_0 img {
            transition: all .2s linear;
        }

        img.gridProductImage:hover {
            scale: 2;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0" style="font-size: 18px">View Recharge Requests</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Receiver Channel</th>
                                            <th class="text-center">User Name</th>
                                            <th class="text-center">Company Name</th>
                                            <th class="text-center">Payment Method</th>
                                            <th class="text-center">Bank Name</th>
                                            <th class="text-center">Account/Cheque</th>
                                            <th class="text-center">Recharge Amount</th>
                                            <th class="text-center">Transaction ID</th>
                                            <th class="text-center">Attachment</th>
                                            <th class="text-center">Submitted On</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('footer_js')
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({

            processing: true,
            serverSide: true,
            stateSave: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            ajax: "{{ url('view/recharge/requests') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: '',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'receiving_channel',
                    name: 'receiving_channel'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'payment_method',
                    name: 'payment_method'
                },
                {
                    data: 'bank_name',
                    name: 'bank_name'
                },
                {
                    data: 'acc_no',
                    name: 'acc_no'
                },
                {
                    data: 'recharge_amount',
                    name: 'recharge_amount'
                },
                {
                    data: 'transaction_id',
                    name: 'transaction_id'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to delete !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/recharge/request') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Recharge Request Deleted");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.approveBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to Approve !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('approve/recharge/request') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Recharge Request Approved");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.denyBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to Deny !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('deny/recharge/request') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Recharge Request Denied");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection
