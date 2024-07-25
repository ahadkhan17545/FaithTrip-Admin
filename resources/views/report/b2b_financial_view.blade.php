<div class="card mt-3" id="printableArea">
    <div class="card-body">

        <div class="row pb-3">
            <div class="col-lg-6">
                <h6 style="font-weight: 600;">
                    B2B Financial Report @if($startDate) From <span class="text-success">{{date("Y-m-d", strtotime($startDate))}}</span> @endif
                    @if($endDate) To <span class="text-success">{{date("Y-m-d", strtotime($endDate))}}</span> @endif
                </h6>
            </div>
            <div class="col-lg-6 text-end">
                <a href="javascript:void(0);" onclick="printPageArea('printableArea')" class="hidden-print btn btn-sm btn-success rounded"> Print Report</a>
            </div>
        </div>

        <table class="table table-striped table-bordered w-100 table-sm">
            <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Registered At</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">User's Name</th>
                    <th class="text-center">Mobile No</th>
                    <th class="text-center">Company</th>
                    <th class="text-center">Comission</th>
                    <th class="text-center">Balance</th>
                    <th class="text-center">Total Recharge</th>

                    <th class="text-center">R. Bookings</th>
                    <th class="text-center">Bookings</th>
                    <th class="text-center">Issued</th>
                    <th class="text-center">B. Cancel</th>
                    <th class="text-center">T. Cancel</th>
                    <th class="text-center">Total Base Fare</th>
                    <th class="text-center">Total Fare Amount</th>
                    <th class="text-center">Comission</th>
                    <th class="text-center">Profit</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $sl = 1;
                    $totalBalance = 0;
                    $totalRechargeAmount = 0;
                    $totalRequestedBookings = 0;
                    $totalflightBookings = 0;
                    $totalTicketIssued = 0;
                    $totalBookingCancelled = 0;
                    $totalTicketCancelled = 0;
                    $grandTotalBaseFare = 0;
                    $grandTotalFare = 0;
                    $totalB2bComissions = 0;
                    $totalProfit = 0;
                @endphp
                @foreach ($data as $item)

                    @php
                        $b2bComission = 0;
                        $profitComission = 0;
                        $allFlightBookingsQuery = DB::table('flight_bookings')->where('booked_by', $item->id)->whereIn('status', [0,1,2]);
                        if($startDate){
                            $allFlightBookingsQuery->where('created_at', '>=', $startDate);
                        }
                        if($startDate){
                            $allFlightBookingsQuery->where('created_at', '<=', $endDate);
                        }
                        $allFlightBookings = $allFlightBookingsQuery->get();

                        foreach ($allFlightBookings as $allFlightBooking) {
                            $b2bComission = $b2bComission + (($allFlightBooking->base_fare_amount*$allFlightBooking->b2b_comission)/100);
                            $profitComission = $profitComission + (($allFlightBooking->base_fare_amount*(7-$allFlightBooking->b2b_comission))/100);
                        }

                        $totalB2bComissions = $totalB2bComissions + $b2bComission;
                        $totalProfit = $totalProfit + $profitComission;
                    @endphp

                    <tr>
                        <td class="text-center">{{$sl++}}</td>
                        <td class="text-center">{{date("d M, Y", strtotime($item->created_at))}}</td>
                        <td class="text-center">
                            @if($item->user_type == 1)
                            Admin
                            @else
                            B2B
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->status == 1)
                            <span style="color: green">Active</span>
                            @else
                            <span style="color: red">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">{{$item->name}}</td>
                        <td class="text-center">{{$item->phone}}</td>
                        <td class="text-center">{{$item->company_name}}</td>
                        <td class="text-center">{{$item->comission}}%</td>
                        <td class="text-end">
                            @php
                                $totalBalance = $totalBalance + $item->balance;
                            @endphp
                            {{number_format($item->balance, 2)}}
                        </td>
                        <td class="text-end">
                            @php
                                $rechargeQuery = DB::table('recharge_requests')->where('user_id', $item->id)->where('status', 1);
                                if($startDate){
                                    $rechargeQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $rechargeQuery->where('created_at', '<=', $endDate);
                                }
                                $totalRecharge = $rechargeQuery->sum('recharge_amount');
                                $totalRechargeAmount = $totalRechargeAmount + $totalRecharge;
                            @endphp
                            {{number_format($totalRecharge, 2)}}
                        </td>

                        <td class="text-center">
                            @php
                                $requestedBookingQuery = DB::table('flight_bookings')->where('booked_by', $item->id);
                                if($startDate){
                                    $requestedBookingQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $requestedBookingQuery->where('created_at', '<=', $endDate);
                                }
                                $requestedBookings = $requestedBookingQuery->where('status', 0)->count();
                                $totalRequestedBookings = $totalRequestedBookings + $requestedBookings;
                                echo $requestedBookings;
                            @endphp
                        </td>
                        <td class="text-center">
                            @php
                                $flightBookingsQuery = DB::table('flight_bookings')->where('booked_by', $item->id);
                                if($startDate){
                                    $flightBookingsQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $flightBookingsQuery->where('created_at', '<=', $endDate);
                                }
                                $flightBookings = $flightBookingsQuery->where('status', 1)->count();
                                $totalflightBookings = $totalflightBookings + $flightBookings;
                                echo $flightBookings;
                            @endphp
                        </td>
                        <td class="text-center">
                            @php
                                $ticketIssuedQuery = DB::table('flight_bookings')->where('booked_by', $item->id);
                                if($startDate){
                                    $ticketIssuedQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $ticketIssuedQuery->where('created_at', '<=', $endDate);
                                }
                                $ticketIssued = $ticketIssuedQuery->where('status', 2)->count();
                                $totalTicketIssued = $totalTicketIssued + $ticketIssued;
                                echo $ticketIssued;
                            @endphp
                        </td>
                        <td class="text-center">
                            @php
                                $bookingCancelledQuery = DB::table('flight_bookings')->where('booked_by', $item->id);
                                if($startDate){
                                    $bookingCancelledQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $bookingCancelledQuery->where('created_at', '<=', $endDate);
                                }
                                $bookingCancelled = $bookingCancelledQuery->where('status', 3)->count();
                                $totalBookingCancelled = $totalBookingCancelled + $bookingCancelled;
                                echo $bookingCancelled;
                            @endphp
                        </td>
                        <td class="text-center">
                            @php
                                $ticketCancelledQuery = DB::table('flight_bookings')->where('booked_by', $item->id);
                                if($startDate){
                                    $ticketCancelledQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $ticketCancelledQuery->where('created_at', '<=', $endDate);
                                }
                                $ticketCancelled = $ticketCancelledQuery->where('status', 4)->count();
                                $totalTicketCancelled = $totalTicketCancelled + $ticketCancelled;
                                echo $ticketCancelled;
                            @endphp
                        </td>

                        <td class="text-end">
                            @php
                                $baseFareQuery = DB::table('flight_bookings')->where('booked_by', $item->id)->whereIn('status', [0,1,2]);
                                if($startDate){
                                    $baseFareQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $baseFareQuery->where('created_at', '<=', $endDate);
                                }
                                $totalBaseFare = $baseFareQuery->sum('base_fare_amount');
                                $grandTotalBaseFare = $grandTotalBaseFare + $totalBaseFare;
                                echo number_format($totalBaseFare, 2);
                            @endphp
                        </td>
                        <td class="text-end">
                            @php
                                $totalFareQuery = DB::table('flight_bookings')->where('booked_by', $item->id)->whereIn('status', [0,1,2]);
                                if($startDate){
                                    $totalFareQuery->where('created_at', '>=', $startDate);
                                }
                                if($startDate){
                                    $totalFareQuery->where('created_at', '<=', $endDate);
                                }
                                $totalFareAmount = $totalFareQuery->sum('total_fare');
                                $grandTotalFare = $grandTotalFare + $totalFareAmount;
                                echo number_format($totalFareAmount, 2);
                            @endphp
                        </td>

                        <td class="text-end">{{number_format($b2bComission, 2)}}</td>
                        <td class="text-end">{{number_format($profitComission, 2)}}</td>
                    </tr>
                @endforeach

                <tr>
                    <th class="text-end" colspan="8">Total : </th>
                    <th class="text-end">৳ {{number_format($totalBalance, 2)}}</th>
                    <th class="text-end">৳ {{number_format($totalRechargeAmount, 2)}}</th>
                    <th class="text-center">{{$totalRequestedBookings}}</th>
                    <th class="text-center">{{$totalflightBookings}}</th>
                    <th class="text-center">{{$totalTicketIssued}}</th>
                    <th class="text-center">{{$totalBookingCancelled}}</th>
                    <th class="text-center">{{$totalTicketCancelled}}</th>
                    <th class="text-end">৳ {{number_format($grandTotalBaseFare, 2)}}</th>
                    <th class="text-end">৳ {{number_format($grandTotalFare, 2)}}</th>
                    <th class="text-end">৳ {{number_format($totalB2bComissions, 2)}}</th>
                    <th class="text-end">৳ {{number_format($totalProfit, 2)}}</th>
                </tr>

            </tbody>
        </table>

    </div>
</div>
