@extends('master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
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
                    <h6 class="mb-0" style="font-size: 18px">View All Office Address</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <label id="customFilter">
                                <button class="btn btn-success btn-sm" id="addNewAddress" style="margin-left: 5px"><b><i
                                            class="fas fa-plus"></i> Add New Office</b></button>
                            </label>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Office Name</th>
                                            <th class="text-center">Office Address</th>
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


    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="productForm2" name="productForm2" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Add New Office Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="officeName">Office Name</label>
                            <input type="text" class="form-control" id="officeName" name="office_name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="officeAddress">Office Address</label>
                            <textarea class="form-control" id="officeAddress" name="office_address" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveBtn" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Update Office Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="office_address_id" id="office_address_id">
                        <div class="form-group mb-3">
                            <label for="officeName2">Office Name</label>
                            <input type="text" class="form-control" id="officeName2" name="office_name_update" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="officeAddress2">Office Address</label>
                            <textarea class="form-control" id="officeAddress2" name="office_address_update" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="office_address_status">Status</label>
                            <select class="form-select" name="office_address_status" id="office_address_status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="updateBtn" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@section('footer_js')
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

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

            ajax: "{{ url('view/office/address') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'office_name',
                    name: 'office_name'
                },
                {
                    data: 'office_address',
                    name: 'office_address'
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

        $(".dataTables_filter").append($("#customFilter"));

        $('#officeAddress').summernote({
            placeholder: 'Type Here',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('#officeAddress2').summernote({
            placeholder: 'Type Here',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#addNewAddress').on('click', function () {
            $('#productForm2').trigger("reset");
            var myModal = new bootstrap.Modal(document.getElementById('exampleModal2'));
            myModal.show();
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('save/office/address') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm2').trigger("reset");

                    // Hide modal using Bootstrap 5 method
                    var modalEl = document.getElementById('exampleModal2');
                    var modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    toastr.success("New Office Address Added", "Added Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Duplicate Color Exists", "Duplicate Color Exists");
                    $('#saveBtn').html('Save');
                }
            });
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to delete !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/office/address') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Office Address Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.editBtn', function () {
            var slug = $(this).data('id');

            $.get("{{ url('get/office/address') }}" + '/' + slug, function (data) {
                // Set values
                $('#office_address_id').val(data.id);
                $('#officeName2').val(data.office_name);
                $('#office_address_status').val(data.status);
                $('#officeAddress2').summernote('code', data.office_address);

                // Show modal using Bootstrap 5 API
                var modalEl = document.getElementById('exampleModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating...');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/office/address') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Update');
                    $('#productForm').trigger("reset");

                    // Hide modal using Bootstrap 5 method
                    var modalEl = document.getElementById('exampleModal');
                    var modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    toastr.success("Office Address Info Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Something went Wrong");
                    $('#updateBtn').html('Save Changes');
                }
            });
        });
    </script>
@endsection
