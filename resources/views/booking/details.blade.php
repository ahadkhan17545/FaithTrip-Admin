@extends('master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-5">
                            <h6 class="mb-0" style="font-size: 18px"><b>Flight Booking Details</b></h6>
                        </div>
                        <div class="col-lg-7 text-end">
                            <a href="{{url('booking/preview')}}/{{$flightBookingDetails->booking_no}}" class="btn btn-sm btn-primary d-inline-block"><i class="fa fa-print"></i> Booking Preview</a>

                            @if($flightBookingDetails->status == 1)
                                <a href="{{url('issue/flight/ticket')}}/{{$flightBookingDetails->pnr_id}}" class="btn btn-sm btn-success d-inline-block"><i class="fas fa-check"></i> Issue Ticket</a>
                                <a href="{{url('cancel/flight/booking')}}/{{$flightBookingDetails->pnr_id}}" class="btn btn-sm btn-danger d-inline-block"><i class="fas fa-ban"></i> Cancel Booking</a>
                            @endif

                            <a href="javascript:void(0)" onclick="sharePnr('{{$flightBookingDetails->pnr_id}}', '{{$flightBookingDetails->traveller_email}}', '{{$flightBookingDetails->traveller_contact}}')" class="btn btn-sm btn-info d-inline-block"><i class="fas fa-share"></i> Share PNR</a>
                        </div>
                    </div>

                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-4 mb-2 border-end">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom" style="font-size: 16px">Booking Info</h6>
                            <table>
                                <tr>
                                    <th>Booking No </th>
                                    <td>: {{ $flightBookingDetails->booking_no }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Date </th>
                                    <td>: {{ date('Y-m-d h:i a', strtotime($flightBookingDetails->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Booked By </th>
                                    <td>: {{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <th>GDS </th>
                                    <td>: {{ $flightBookingDetails->gds }}</td>
                                </tr>
                                <tr>
                                    <th>GDS ID </th>
                                    <td>: {{ $flightBookingDetails->gds_unique_id }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 mb-2 border-end">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom" style="font-size: 16px">Traveller Info</h6>
                            <table>
                                <tr>
                                    <th>PNR ID </th>
                                    <td>: {{ $flightBookingDetails->pnr_id }}</td>
                                </tr>
                                <tr>
                                    <th>Name </th>
                                    <td>: {{ $flightBookingDetails->traveller_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email </th>
                                    <td>: {{ $flightBookingDetails->traveller_email }}</td>
                                </tr>
                                <tr>
                                    <th>Contact </th>
                                    <td>: {{ $flightBookingDetails->traveller_contact }}</td>
                                </tr>
                                <tr>
                                    <th>Total Passanger </th>
                                    <td>:
                                        @if ($flightBookingDetails->adult)
                                            {{ $flightBookingDetails->adult }} Adult
                                            @endif @if ($flightBookingDetails->child)
                                                {{ $flightBookingDetails->child }} Child
                                                @endif @if ($flightBookingDetails->infant)
                                                    {{ $flightBookingDetails->infant }} Infant
                                                @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <h6 class="fw-bold mb-2 pb-1 border-bottom" style="font-size: 16px">Flight Info</h6>
                            <table>
                                <tr>
                                    <th>Departure </th>
                                    <td>: {{ $flightBookingDetails->departure_date }}</td>
                                </tr>
                                <tr>
                                    <th>From </th>
                                    <td>: {{ $flightBookingDetails->departure_location }}</td>
                                </tr>
                                <tr>
                                    <th>To </th>
                                    <td>: {{ $flightBookingDetails->arrival_location }}</td>
                                </tr>
                                <tr>
                                    <th>Total Fare </th>
                                    <td>: {{ $flightBookingDetails->currency }}
                                        {{ number_format($flightBookingDetails->total_fare) }}</td>
                                </tr>
                                <tr>
                                    <th>Status </th>
                                    <td>:
                                        @if ($flightBookingDetails->status == 1)
                                            <span style="color: green; font-weight: 600">Booking Done</span>
                                        @endif
                                        @if ($flightBookingDetails->status == 2)
                                            <span style="color: green; font-weight: 600">Ticket Issued</span>
                                        @endif
                                        @if ($flightBookingDetails->status == 3)
                                            <span style="color: red; font-weight: 600">Booking Cancelled</span>
                                        @endif
                                        @if ($flightBookingDetails->status == 4)
                                            <span style="color: red; font-weight: 600">Ticket Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <table class="table table-bordered border-dark table-sm table-striped table-hover">
                                <thead>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center" colspan="14" style="font-size: 14px">
                                            Flight Segments</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center">Sl</th>
                                        <th scope="col" class="text-center">Departure</th>
                                        <th scope="col" class="text-center">From</th>
                                        <th scope="col" class="text-center">Terminal</th>
                                        <th scope="col" class="text-center">Elapsed Time</th>
                                        <th scope="col" class="text-center">Arrival</th>
                                        <th scope="col" class="text-center">To</th>
                                        <th scope="col" class="text-center">Terminal</th>
                                        <th scope="col" class="text-center">Baggage</th>
                                        <th scope="col" class="text-center">Operating Flight</th>
                                        <th scope="col" class="text-center">Marketing Flight</th>
                                        <th scope="col" class="text-center">Booking Code</th>
                                        <th scope="col" class="text-center">Cabin Code</th>
                                        <th scope="col" class="text-center">Total Miles</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($flightSegments as $index => $segment)
                                        <tr>
                                            <th scope="row">{{ $index + 1 }}</th>
                                            <td class="text-center">{{ $segment->departure_time }}</td>
                                            <td class="text-center">{{ $segment->departure_airport_code }}</td>
                                            <td class="text-center">{{ $segment->departure_terminal }}</td>
                                            <td class="text-center">{{ $segment->elapsed_time }} mins</td>
                                            <td class="text-center">{{ $segment->arrival_time }}</td>
                                            <td class="text-center">{{ $segment->arrival_airport_code }}</td>
                                            <td class="text-center">{{ $segment->arrival_terminal }}</td>
                                            <td class="text-center">{{ $segment->baggage_allowance }}</td>
                                            <td class="text-center">
                                                {{ $segment->carrier_operating_code }}-{{ $segment->carrier_operating_flight_number }}
                                            </td>
                                            <td class="text-center">
                                                {{ $segment->carrier_marketing_code }}-{{ $segment->carrier_marketing_flight_number }}
                                            </td>
                                            <td class="text-center">{{ $segment->booking_code }}</td>
                                            <td class="text-center">{{ $segment->cabin_code }}</td>
                                            <td class="text-center">{{ $segment->total_miles_flown }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-9">
                            <table class="table table-bordered border-dark table-sm table-striped table-hover">
                                <thead>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center" colspan="9" style="font-size: 14px">
                                            Flight Passangers</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center">Sl</th>
                                        <th scope="col" class="text-center">Type</th>
                                        <th scope="col" class="text-center">Name</th>
                                        <th scope="col" class="text-center">DOB</th>
                                        <th scope="col" class="text-center">Doc Type</th>
                                        <th scope="col" class="text-center">Doc No</th>
                                        <th scope="col" class="text-center">Expire Date</th>
                                        <th scope="col" class="text-center">Issued By</th>
                                        <th scope="col" class="text-center">Nationality</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($flightPassangers as $passangerIndex => $flightPassanger)
                                        <tr>
                                            <th scope="row">{{ $passangerIndex + 1 }}</th>
                                            <td class="text-center">{{ $flightPassanger->passanger_type }}</td>
                                            <td class="text-center">{{ $flightPassanger->first_name }}
                                                {{ $flightPassanger->last_name }}</td>
                                            <td class="text-center">{{ $flightPassanger->dob }}</td>
                                            <td class="text-center">
                                                @if ($flightPassanger->document_type == 1)
                                                    Passport
                                                @else
                                                    National ID
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $flightPassanger->document_no }}</td>
                                            <td class="text-center">{{ $flightPassanger->document_expire_date }}</td>
                                            <td class="text-center">{{ $flightPassanger->document_issue_country }}
                                            </td>
                                            <td class="text-center">{{ $flightPassanger->nationality }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-3 text-end pt-2">
                            <table style="width: 100%">
                                <tr>
                                    <td class="text-end">
                                        <h5 style="font-size: 16px; font-weight: 600;">Base Fare :</h5>
                                    </td>
                                    <td class="text-end">
                                        <h5 style="font-size: 16px; font-weight: 600;">
                                            {{ number_format($flightBookingDetails->base_fare_amount) }}
                                            {{ $flightBookingDetails->currency }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        <h5 style="font-size: 16px; font-weight: 600;">Total Tax :</h5>
                                    </td>
                                    <td class="text-end">
                                        <h5 style="font-size: 16px; font-weight: 600;">
                                            {{ number_format($flightBookingDetails->total_tax_amount) }}
                                            {{ $flightBookingDetails->currency }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">
                                        <h5 style="font-size: 20px; font-weight: 600;">Total Fare :</h5>
                                    </td>
                                    <td class="text-end">
                                        <h5 style="font-size: 20px; font-weight: 600;">
                                            {{ number_format($flightBookingDetails->total_fare) }}
                                            {{ $flightBookingDetails->currency }}</h5>
                                    </td>
                                </tr>
                            </table>
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
                    <h5 class="modal-title">Send PNR Copy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm2" name="productForm2" class="form-horizontal">
                        <div class="form-group mb-3">
                            <label for="pnr_id">PNR ID</label>
                            <input type="text" id="pnr_id" class="form-control" placeholder="PNR ID" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="traveller_email">Traveller Email</label>
                            <input type="text" id="traveller_email" class="form-control" placeholder="traveller@email.com">
                        </div>
                        <div class="form-group">
                            <label for="traveller_contact">Traveller Contact</label>
                            <input type="text" id="traveller_contact" class="form-control" placeholder="8801*********">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveBtn" class="btn btn-primary">Send PNR</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script>
        function sharePnr(pnr_id, traveller_email, traveller_contact){
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
            $("#pnr_id").val(pnr_id);
            $("#traveller_email").val(traveller_email);
            $("#traveller_contact").val(traveller_contact);
        }

        $('#saveBtn').click(function (e) {
            $('#exampleModal2').modal('hide');
            toastr.success("PNR Shared Successfully", "Sent");
        });
    </script>
@endsection
