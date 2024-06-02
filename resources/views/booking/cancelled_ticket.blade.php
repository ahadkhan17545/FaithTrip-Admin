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
                    <h6 class="mb-0" style="font-size: 18px">View All Cancelled Tickets</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-bordered mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Booking Date</th>
                                            <th class="text-center">PNR</th>
                                            <th class="text-center">Departure</th>
                                            <th class="text-center">From</th>
                                            <th class="text-center">To</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Passanger</th>
                                            <th class="text-center">Total Fare</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            {{-- </div> --}}

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
            ajax: "{{ url('view/cancelled/tickets') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'pnr_id',
                    name: 'pnr_id'
                },
                {
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'departure_location',
                    name: 'departure_location'
                },
                {
                    data: 'arrival_location',
                    name: 'arrival_location'
                },
                {
                    data: 'traveller_contact',
                    name: 'traveller_contact'
                },
                {
                    data: 'total_passangers',
                    name: 'total_passangers'
                },
                {
                    data: 'total_fare',
                    name: 'total_fare'
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
    </script>
@endsection
