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
                            @if($flightBookingDetails->gds == 'Sabre')
                            <a href="{{url('booking/preview')}}/{{$flightBookingDetails->booking_no}}" class="btn btn-sm btn-primary d-inline-block"><i class="fa fa-print"></i> Booking Preview</a>
                            @endif

                            @if($flightBookingDetails->status == 1)
                                <a href="{{url('issue/flight/ticket')}}/{{$flightBookingDetails->booking_no}}" class="btn btn-sm btn-success d-inline-block"><i class="fas fa-check"></i> Issue Ticket</a>
                                <a href="{{url('cancel/flight/booking')}}/{{$flightBookingDetails->booking_no}}" class="btn btn-sm btn-danger d-inline-block"><i class="fas fa-ban"></i> Cancel Booking</a>
                            @endif

                            @if($flightBookingDetails->status == 2)
                                <a href="{{url('cancel/issued/ticket')}}/{{$flightBookingDetails->booking_no}}" class="btn btn-sm btn-danger d-inline-block"><i class="fas fa-ban"></i> Cancel Ticket</a>
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
                                    <th>Source </th>
                                    <td>: @if($flightBookingDetails->source == 1) FaithTrip Portal @elseif($flightBookingDetails->source == 2) Website @else Mobile App @endif</td>
                                </tr>
                                @if($flightBookingDetails->payment_status)
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        @if($flightBookingDetails->payment_status == 0)
                                            : Pending
                                        @elseif($flightBookingDetails->payment_status == 1)
                                            : Paid (@if($flightBookingDetails->payment_method == 1) SSLCommerz @elseif($flightBookingDetails->payment_method == 2) bkash @else Nagad @endif)
                                        @else
                                            : Failed
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($flightBookingDetails->transaction_id)
                                <tr>
                                    <th>Transaction ID</th>
                                    <td>
                                        : {{ $flightBookingDetails->transaction_id }}
                                    </td>
                                </tr>
                                @endif

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
                                    @php
                                        $bookedByUser = DB::table('users')->where('id', $flightBookingDetails->booked_by)->first();
                                    @endphp
                                    <td>: {{ $bookedByUser ? $bookedByUser->name : 'Passanger' }}</td>
                                </tr>

                                @if($flightBookingDetails->passanger_id)
                                <tr>
                                    <th>Passanger Acc. </th>
                                    <td>
                                        @php
                                            $userInfo = DB::table('users')->where('id', $flightBookingDetails->passanger_id)->first();
                                            if($userInfo){
                                                echo ': '.$userInfo->name."(".$userInfo->email.")";
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                @endif

                                <tr>
                                    <th>GDS Info</th>
                                    <td>: {{ $flightBookingDetails->gds }} ({{ $flightBookingDetails->gds_unique_id }})</td>
                                </tr>
                                <tr>
                                    <th>Booking Mode </th>
                                    <td>: @if($flightBookingDetails->is_live == 1) <span style="padding: 0px 8px; border-radius: 4px; background: green; color: white;">Live</span> @else <span style="padding: 0px 8px; border-radius: 4px; background: #db0000; color: white;">Sandbox</span> @endif</td>
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
                                @if($flightBookingDetails->pnr_id != $flightBookingDetails->booking_id)
                                <tr>
                                    <th>Booking ID </th>
                                    <td>: {{ $flightBookingDetails->booking_id }}</td>
                                </tr>
                                @endif

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
                                    <th>Flight Routes</th>
                                    <td>
                                        : {{ $flightBookingDetails->departure_location }} - {{ $flightBookingDetails->arrival_location }} @if($flightBookingDetails->flight_type == 2) - {{ $flightBookingDetails->departure_location }} @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Departure Date</th>
                                    <td>
                                        @php
                                            $departure = $bookingResSegs ? $bookingResSegs[0]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                                            $departureDateTime = explode('T', $departure);
                                            if(isset($departureDateTime[0]) && isset($departureDateTime[1])){
                                                echo ": ".date('j M Y', strtotime($departureDateTime[0]))." ".substr($departureDateTime[1], 0, 5);
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <th>Arrival Date</th>
                                    <td>
                                        @php
                                            $arrival = $bookingResSegs ? $bookingResSegs[count($flightSegments)-1]['Product']['ProductDetails']['Air']['ArrivalDateTime'] : null;
                                            $arrivalDateTime = explode('T', $arrival);
                                            if(isset($arrivalDateTime[0]) && isset($arrivalDateTime[1])){
                                                echo ": ".date('j M Y', strtotime($arrivalDateTime[0]))." ".substr($arrivalDateTime[1], 0, 5);
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Fare </th>
                                    <td>: {{ $flightBookingDetails->currency }}
                                        {{ number_format($flightBookingDetails->total_fare) }}</td>
                                </tr>
                                <tr>
                                    <th>Status </th>
                                    <td>:
                                        @if ($flightBookingDetails->status == 0)
                                            <span style="color: goldenrod; font-weight: 600">Booking Requested</span>
                                        @endif
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

                                @if($flightPassangers[0]->ticket_no == null)
                                <tr>
                                    <th>Last Ticket Datetime </th>
                                    <td>
                                        @if($flightBookingDetails->last_ticket_datetime)
                                        : {{ date("jS M-y, h:i a", strtotime($flightBookingDetails->last_ticket_datetime)) }}
                                        @else
                                        : <a href="{{url('flight/booking/details')}}/{{$flightBookingDetails->booking_no}}" style="padding: 0px 10px; text-shadow: 1px 1px 2px black;" class="btn btn-sm btn-success rounded">Try Again</a>
                                        @endif
                                    </td>
                                </tr>
                                @endif

                            </table>

                            @if($flightBookingDetails->status == 1 && !$flightBookingDetails->last_ticket_datetime)
                            <small class="d-block text-danger mt-3">N/B: Airlines does not share Last Ticket Datetime instantly right after PNR creation</small>
                            @endif

                        </div>
                    </div>
                    <hr>

                    @include('booking.segments')

                    <div class="row">
                        <div class="col-lg-9">
                            <table class="table table-bordered border-dark table-sm table-striped table-hover">
                                <thead>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center" colspan="10" style="font-size: 14px">Flight Passangers</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th scope="col" class="text-center">Sl</th>
                                        <th scope="col" class="text-center">Ticket No</th>
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
                                            <td class="text-center">@if($flightPassanger->ticket_no){{ $flightPassanger->ticket_no }}@else N/A @endif</td>
                                            <td class="text-center">{{ $flightPassanger->passanger_type }}</td>
                                            <td class="text-center">{{ $flightPassanger->title }} {{ $flightPassanger->first_name }} {{ $flightPassanger->last_name }}</td>
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
                                            <td class="text-center">{{ $flightPassanger->document_issue_country }}</td>
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

                    @if($flightBookingDetails->status == 0)
                    <hr>
                    <h5>For Booking Requested Flights Only</h5>
                    <div class="row">
                        <div class="col-lg-7">
                            In Sandbox Environment Some flights cannot be booked throught Automation. You need to book them manually and update the PNR ID for later processing like Ticket Issue or Cancel Booking. During Sandbox enviroment system can book BG, BS, EK, QR, TK etc. So please try to test booking in between these airlines.
                        </div>
                        <div class="col-lg-5">
                            <form action="{{url('update/pnr/booking')}}" method="POST">
                                @csrf
                                <input type="hidden" name="booking_no" value="{{$flightBookingDetails->booking_no}}">
                                <div class="row">
                                    <div class="co-lg-12 mb-2">
                                        <input type="text" class="form-control" name="pnr_id" placeholder="PNR ID" required>
                                    </div>
                                    <div class="col-lg-7">
                                        <select class="form-select" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="1">Booking Done</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-primary w-100">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

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
