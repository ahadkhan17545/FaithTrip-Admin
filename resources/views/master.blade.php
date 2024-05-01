<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FaithTrip</title>
    <meta name="description" content="FaithTrip is an OTA" />
    <meta name="robots" content="all" />
    <meta property="og:title" content="FaithTrip" />
    <meta property="og:description" content="FaithTrip is an OTA" />
    <meta property="og:url" content="FaithTrip is an OTA" />
    <meta property="og:type" content="WebPage" />
    <meta property="og:site_name" content="FaithTrip" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('assets') }}/img/favicon.svg" />

    <!-- Theme CSS -->
    <link href="{{ url('assets') }}/admin-assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/vendor/metisMenu/metisMenu.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/vendor/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/vendor/typicons/src/typicons.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/vendor/themify-icons/themify-icons.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/vendor/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/t-datepicker-master/public/theme/css/t-datepicker.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/t-datepicker-master/public/theme/css/themes/t-datepicker-main.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets//css/search.css?v=1" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/fontawesome-free-6.3.0-web/css/all.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/vendor/toastr/build/toastr.min.css?v=6" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/css/arrow-hidden.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/nanopkg-assets/css/custom.min.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/css/style-new.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/css/custom.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/admin-assets/css/extra.css" rel="stylesheet" />
    <link href="{{ url('assets') }}/module-assets//css/booking/search_box.css?v=8" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/module-assets//css/booking/search_box_custom.min.css?v=8" rel="stylesheet" type="text/css" />

    <style>
        .body-content {
            padding: 1.5rem;
        }
    </style>
    @yield('header_css')

</head>

<body data-departure="Departure" data-return="Return" class="fixed sidebar-mini" data-app-config>

    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>

    <div x-data="{ m: false }">
        <div class="wrapper">

            @include('sidebar')

            <div class="content-wrapper">
                <div class="main-content">

                    @include('header')

                    <div class="body-content">

                        @yield('content')

                    </div>
                </div>

                <div class="overlay"></div>

                @include('footer')

            </div>

        </div>
    </div>

    <div class="modal fade" id="delete-modal" data-bs-keyboard="false" tabindex="-1" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0);" class="needs-validation" id="delete-modal-form">
                        <div class="modal-body">
                            <p>
                                Are you sure you want to delete this item? you won t be able
                                to revert this item back!
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button class="btn btn-danger" type="submit" id="delete_submit">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme JS -->
    <script src="{{ url('assets') }}/admin-assets/vendor/jQuery/jquery.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/select2/dist/js/select2.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/vendor/t-datepicker-master/public/theme/js/t-datepicker.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/metisMenu/metisMenu.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/vendor/fontawesome-free-6.3.0-web/js/all.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/vendor/toastr/build/toastr.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/vendor/axios/dist/axios.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/axios.init.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/arrow-hidden.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/img-src.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/delete.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/user-status-update.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/moment/moment.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/vendor/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/js/ajax_form_submission.min.js"></script>
    {{-- <script src="{{ url('assets') }}/module-assets/js/booking/search_box.js"></script> --}}
    <script src="{{ url('assets') }}/module-assets/js/booking/booking.min.js"></script>
    <script src="{{ url('assets') }}/module-assets/js/setting/setting.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/main.min.js"></script>
    <script src="{{ url('assets') }}/admin-assets/js/sidebar.min.js"></script>
    <script src="{{ url('assets') }}/nanopkg-assets/js/tosterSession.min.js"></script>
    <script defer src="{{ url('assets') }}/nanopkg-assets/vendor/alpine/alpine.min.js"></script>

    @yield('footer_js')

</body>

</html>
