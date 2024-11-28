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
                    <h5 class="alert-heading mb-0">Airlines Comissions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            {{-- <div class="table-responsive"> --}}
                                <table class="table table-bordered mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Airline Name</th>
                                            <th class="text-center">Airline Code</th>
                                            <th class="text-center">Country</th>
                                            <th class="text-center">Comission (%)</th>
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


    <div class="modal fade" id="exampleModal2" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Airline Comission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm2" name="productForm2" class="form-horizontal">
                        <input type="hidden" id="airline_id">
                        <div class="form-group mb-3">
                            <label for="airline_name">Airline Name</label>
                            <input type="text" id="airline_name" class="form-control" placeholder="Example Airline" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="airline_code">Airline Code</label>
                            <input type="text" id="airline_code" class="form-control" placeholder="EA" readonly>
                        </div>
                        <div class="form-group">
                            <label for="airline_comission">Comission</label>
                            <input type="number" id="airline_comission" class="form-control" placeholder="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveBtn" class="btn btn-primary">Save Comission</button>
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
            ajax: "{{ url('view/airlines/comissions') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'iata',
                    name: 'iata'
                },
                {
                    data: 'country',
                    name: 'country'
                },
                {
                    data: 'comission',
                    name: 'comission'
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

        $('#saveBtn').click(function (e) {
            e.preventDefault();

            var formData = new FormData();
            formData.append("airline_id", $("#airline_id").val());
            formData.append("airline_comission", $("#airline_comission").val());

            $(this).html('Saving..');
            $.ajax({
                data: formData,
                url: "{{ url('update/airline/comission') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#saveBtn').html('Save Comission');
                    $('#exampleModal2').modal('hide');
                    table.draw(false);
                    toastr.success("Airline Comission Updated");
                    $('#productForm2').trigger("reset");
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.error("Something went wrong");
                    $('#saveBtn').html('Try Again');
                }
            });

        });


        $('body').on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('airline/info') }}" +'/' + id, function (data) {
                $('#exampleModal2').modal('show');
                $('#airline_id').val(id);
                $('#airline_name').val(data.name);
                $('#airline_code').val(data.iata);
                $('#airline_comission').val(data.comission);
            })
        });

    </script>
@endsection
