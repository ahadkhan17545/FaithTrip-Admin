<div class="card mt-3" id="printableArea">
    <div class="card-body" style="overflow-x: scroll">

        <div class="row pb-3">
            <div class="col-lg-6">
                <h6 style="font-weight: 600;">
                    Flight Booking Report @if($startDate) From <span class="text-success">{{date("Y-m-d", strtotime($startDate))}}</span> @endif
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
                    <th class="text-center">Booking No</th>
                    <th class="text-center">Booking Date</th>
                    <th class="text-center">PNR ID</th>
                    <th class="text-center">Travel Date</th>
                    <th class="text-center">Traveller Name</th>
                    <th class="text-center">Mobile No</th>
                    <th class="text-center">Destination</th>
                    <th class="text-center">Arrival</th>
                    <th class="text-center">Base Fare (৳)</th>
                    <th class="text-center">TAX (৳)</th>
                    <th class="text-center">Total Fare (৳)</th>
                    <th class="text-center">Booked By</th>
                    <th class="text-center">B2B Comission</th>
                    <th class="text-center">Profit (৳)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center hidden-print">Details</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $sl = 1;
                    $totalBaseFare = 0;
                    $totalTaxAmount = 0;
                    $totalFareAmount = 0;
                    $totalB2bComission = 0;
                    $totalProfit = 0;
                @endphp
                @foreach ($data as $item)
                    @php
                        if(in_array($item->status, [0,1,2])){
                            $totalBaseFare = $totalBaseFare + $item->base_fare_amount;
                            $totalTaxAmount = $totalTaxAmount + $item->total_tax_amount;
                            $totalFareAmount = $totalFareAmount + $item->total_fare;
                        }

                        $calculatedComission = ($item->base_fare_amount*$item->b2b_comission)/100;
                        if(in_array($item->status, [0,1,2]))
                            $totalB2bComission = $totalB2bComission + $calculatedComission;

                        $profitComissionPercentage = 7-$item->b2b_comission;
                        $profitComissionCalculated = (($item->base_fare_amount*$profitComissionPercentage)/100);
                        if(in_array($item->status, [0,1,2]))
                            $totalProfit = $totalProfit + $profitComissionCalculated;
                    @endphp
                <tr>
                    <td class="text-center">{{$sl++}}</td>
                    <td class="text-center">{{$item->booking_no}}</td>
                    <td class="text-center">{{date("d M, Y", strtotime($item->created_at))}}</td>
                    <td class="text-center">{{$item->pnr_id}}</td>
                    <td class="text-center">{{date("d M, Y", strtotime($item->departure_date))}}</td>

                    <td class="text-center">{{$item->traveller_name}}</td>
                    <td class="text-center">{{$item->traveller_contact}}</td>
                    <td class="text-center">{{$item->departure_location}}</td>
                    <td class="text-center">{{$item->arrival_location}}</td>
                    <td class="text-end" @if(in_array($item->status, [0,1,2])) style="color:green" @else style="color:red" @endif>{{number_format($item->base_fare_amount, 2)}}</td>
                    <td class="text-end" @if(in_array($item->status, [0,1,2])) style="color:green" @else style="color:red" @endif>{{number_format($item->total_tax_amount, 2)}}</td>
                    <td class="text-end" @if(in_array($item->status, [0,1,2])) style="color:green" @else style="color:red" @endif>{{number_format($item->total_fare, 2)}}</td>
                    <td class="text-center">{{$item->b2b_user_name}}</td>
                    <td class="text-end">
                        @if(in_array($item->status, [0,1,2]))
                        <span style="color: green">({{$item->b2b_comission}}%) {{number_format($calculatedComission, 2)}}</span>
                        @else
                        <span class="text-danger">N/A</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if(in_array($item->status, [0,1,2]))
                        <span style="color: green">({{$profitComissionPercentage}}%) {{number_format($profitComissionCalculated, 2)}}</span>
                        @else
                        <span class="text-danger">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            if($item->status == 0)
                                echo "<span style='font-weight:600; color:goldenrod'>Booking Req.</span>";
                            if($item->status == 1)
                                echo "<span style='font-weight:600; color:green'>Booked</span>";
                            if($item->status == 2)
                                echo "<span style='font-weight:600; color:green'>Issued</span>";
                            if($item->status == 3)
                                echo "<span style='font-weight:600; color:red'>B. Cancel</span>";
                            if($item->status == 4)
                                echo "<span style='font-weight:600; color:red'>T. Cancel</span>";
                        @endphp
                    </td>
                    <td class="text-center hidden-print">
                        <a href="{{url('flight/booking/details')}}/{{$item->booking_no}}" target="_blank" class="btn btn-sm btn-primary rounded" style="font-size: 12px; padding: 1px 10px;">Details</a>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th class="text-end" colspan="10">Total : </th>
                    <th class="text-end" style="color: green">৳{{number_format($totalBaseFare, 2)}}</th>
                    <th class="text-end" style="color: green">৳{{number_format($totalTaxAmount, 2)}}</th>
                    <th class="text-end" style="color: green">৳{{number_format($totalFareAmount, 2)}}</th>
                    <th class="text-end" style="color: green">৳{{number_format($totalB2bComission, 2)}}</th>
                    <th class="text-end" style="color: green">৳{{number_format($totalProfit, 2)}}</th>
                    <th colspan="2"></th>
                </tr>

            </tbody>
        </table>

    </div>
</div>
