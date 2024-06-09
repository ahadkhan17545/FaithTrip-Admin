@extends('master')

@section('content')
    <div class="row">
        <div class="col-xl-8 mainContent">
            <div class="theiaStickySidebar">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body">

                        @php

                            // echo "<pre>";
                            // print_r($revlidatedData);
                            // echo "</pre>";

                            $segmentArray = [];
                            $legsArray = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['legs'];
                            foreach ($legsArray as $key => $leg) {
                                $legRef = $leg['ref'] - 1;
                                $legDescription = $revlidatedData['groupedItineraryResponse']['legDescs'][$legRef];
                                $schedulesArray = $legDescription['schedules'];
                                foreach ($schedulesArray as $schedule) {
                                    $scheduleRef = $schedule['ref'] - 1;
                                    $segmentArray[] = $revlidatedData['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
                                }
                            }
                        @endphp

                        @foreach ($segmentArray as $segmentIndex => $segmentData)
                            <div class="flight-info border rounded mb-2">
                                <div class="flight-scroll review-article">
                                    <div class="align-items-center d-flex custom-gap justify-content-between w-100">
                                        <div class="align-items-center d-flex gap-4 text-center">
                                            <div class="brand-img">
                                                <img src="{{ url('airlines_logo') }}/{{ strtolower($segmentData['carrier']['operating']) }}.png">
                                            </div>
                                            <div class="airline-box">
                                                <div class="font-weight-600 fs-13">
                                                    {{ $segmentData['carrier']['operating'] }}
                                                </div>
                                                <div class="font-weight-600 fs-13 text-muted w-max-content">
                                                    {{ $segmentData['carrier']['operatingFlightNumber'] }}
                                                    -
                                                    {{ $segmentData['carrier']['equipment']['code'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-weight-600 fs-13">
                                                {{ $segmentData['departure']['airport'] }}
                                            </div>
                                            <span class="fs-12 font-weight-600">{{ $segmentData['departure']['time'] }}</span><br>
                                            <span class="text-muted fs-12">
                                                Terminal -
                                                {{ isset($segmentData['departure']['terminal']) ? $segmentData['departure']['terminal'] : 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-weight-600 fs-13">
                                                {{ $segmentData['arrival']['airport'] }}
                                            </div>
                                            <span class="fs-12 font-weight-600">{{ $segmentData['arrival']['time'] }}</span><br>
                                            <span class="text-muted fs-12">
                                                Terminal -
                                                {{ isset($segmentData['arrival']['terminal']) ? $segmentData['arrival']['terminal'] : 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="text-center fs-14 w-100">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="d-inline-flex align-items-center w-max-content">
                                                    {{ App\Models\CustomFunction::convertMinToHrMin($segmentData['elapsedTime']) }}
                                                </span>
                                                <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                                    {{ isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode']) ? 'Meal - ' . $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['mealCode'] : 'N/A' }}
                                                </span>
                                                <span class="d-inline-flex align-items-center w-max-content">&nbsp;<span class="text-muted">|</span>&nbsp;
                                                    {{ isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode']) ? 'Booking Code - ' . $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'] : 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="two-dots my-3 text-muted position-relative border-top">
                                                <span class="flight-service">
                                                    <span class="type-text px-2 position-relative">Flight</span>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="d-inline-flex align-items-center w-max-content">
                                                    @php
                                                        $passangerWisebaggage = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'];

                                                        foreach ($passangerWisebaggage as $passangerWisebaggageInfo) {
                                                            if (isset($passangerWisebaggageInfo['passengerInfo']['baggageInformation'][0]['allowance']['ref'])) {

                                                                $baggageRef = $passangerWisebaggageInfo['passengerInfo']['baggageInformation'][0]['allowance']['ref'];
                                                                if (isset($revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1])) {
                                                                    echo $passangerWisebaggageInfo['passengerInfo']['passengerType'] . '(' . $passangerWisebaggageInfo['passengerInfo']['passengerNumber'] . '): ';

                                                                    if (isset($revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'])) {
                                                                        echo 'Piece Count: ' . $revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['pieceCount'] * $passangerWisebaggageInfo['passengerInfo']['passengerNumber'];
                                                                    }
                                                                    if (isset($revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'])) {
                                                                        echo $revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['weight'] * $passangerWisebaggageInfo['passengerInfo']['passengerNumber'];
                                                                    }
                                                                    if (isset($revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'])) {
                                                                        echo ' ' . $revlidatedData['groupedItineraryResponse']['baggageAllowanceDescs'][$baggageRef - 1]['unit'];
                                                                    }

                                                                    echo '&nbsp;&nbsp;';
                                                                }
                                                            }
                                                        }

                                                    @endphp
                                                    &nbsp;
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($segmentArray[$segmentIndex+1]) && isset($segmentData['arrival']['time']) && $segmentArray[$segmentIndex+1]['departure']['time'])
                            <div class="d-flex justify-center px-3">
                                <span class="fs-12 layover text-center">
                                    @php
                                        $time1 = substr($segmentData['arrival']['time'],0,8);
                                        $time2 = substr($segmentArray[$segmentIndex+1]['departure']['time'],0,8);
                                        $time1Obj = DateTime::createFromFormat('H:i:s', $time1);
                                        $time2Obj = DateTime::createFromFormat('H:i:s', $time2);
                                        $interval = $time1Obj->diff($time2Obj);
                                        $formattedDifference = sprintf(
                                            "%dhr %dmin",
                                            $interval->h + ($interval->days * 24), // Total hours, including days if any
                                            $interval->i // Minutes
                                        );
                                        echo $formattedDifference." Layover";
                                    @endphp
                                </span>
                            </div>
                            @endif
                        @endforeach

                    </div>
                </div>
                <form id="submit_ticket_reservation_info" action="{{url('create/pnr/with/booking')}}" method="POST" class="on-submit">
                    @csrf

                    @php
                        session(['revlidatedData' => $revlidatedData]);
                    @endphp

                    <input type="hidden" name="gds" value="Sabre">
                    <input type="hidden" name="gds_unique_id" value="SOOL">
                    <input type="hidden" name="departure_date" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']}}">
                    <input type="hidden" name="departure_location" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureLocation']}}">
                    @php
                        $legDescriptionsLastIndex = count($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'])-1;
                    @endphp
                    <input type="hidden" name="arrival_location" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][$legDescriptionsLastIndex]['arrivalLocation']}}">
                    <input type="hidden" name="governing_carriers" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['governingCarriers']}}">
                    <input type="hidden" name="currency" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency']}}">

                    @if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['lastTicketDate']))
                        <input type="hidden" name="last_ticket_datetime" value="{{$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['lastTicketDate']." ".$revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['lastTicketTime'].":00"}}">
                    @else
                        <input type="hidden" name="last_ticket_datetime" value="">
                    @endif

                    {{-- pricing info start --}}
                    <div class="card shadow border-0 mb-3 d-xl-none">
                        <div class="card-body">
                            <h3 class="fs-17 mb-0">Fare summary</h3>
                            <p class="fs-14">
                                Travellers :
                                @foreach ($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
                                    <b>
                                        {{ $passengerData['passengerInfo']['passengerNumber'] }}
                                        {{ $passengerData['passengerInfo']['passengerType'] }}
                                    </b>&nbsp;
                                @endforeach
                            </p>
                            <hr>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="summary-text">
                                    <div class="font-weight-500">Base Fare</div>
                                </div>
                                <div class="fs-16 font-weight-500" style="font-weight: 600;">
                                    ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                    {{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareAmount'] }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="summary-text">
                                    <div class="font-weight-500">Total Tax Amount</div>
                                </div>
                                <div class="fs-16 font-weight-500" style="font-weight: 600;">
                                    ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                    {{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalTaxAmount'] }}
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="">
                                    <div class="fs-14 font-weight-300">Total Payable Amount</div>
                                </div>
                                <div class="fs-16 font-weight-500">
                                    <span class="ml-2 text-primary" style="font-weight: 600;">
                                        ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                        {{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalPrice'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- pricing info end --}}


                    <div class="card shadow border-0 mb-3">
                        <div class="content-header media mb-3">
                            <div class="media-body">
                                <h3 class="content-header_title fs-23 mb-0">Traveler details</h3>
                                <p>Please provide real information otherwise ticket will not issue</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    <label for="Email">Name</label>
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3">
                                    <input name="traveller_name" type="text" class="form-control" placeholder="Traveller Name" required="">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    <label for="Email">Email</label>
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3">
                                    <input name="traveller_email" type="email" class="form-control" placeholder="Email id" required="">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-2 mb-md-0 pr-3 px-3">
                                    Contact Number <span class="text-danger">*</span>
                                </div>
                                <div class="col-12 col-md-8 mb-2 mb-sm-3">
                                    <input name="traveller_contact" type="text" class="form-control" placeholder="+8801*********" required="">
                                </div>
                            </div>

                            @php $passangerTitleIndex=0; @endphp
                            @foreach ($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerInfoList)
                                @for ($i=1; $i<=$passengerInfoList['passengerInfo']['passengerNumber']; $i++)
                                <hr>
                                <h6 class="fw-bold">Please fill the information for {{$passengerInfoList['passengerInfo']['passengerType']}} - {{$i}}</h6>
                                <input type="hidden" name="passanger_type[]" value="{{$passengerInfoList['passengerInfo']['passengerType']}}">
                                {{-- <div class="form-row mt-3">
                                    <div class="col-sm-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Passenger Title <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-sm-6 col-md-6 mb-3 mb-sm-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{$passengerInfoList['passengerInfo']['passengerType']}}[]" id="inlineRadio0_{{$passangerTitleIndex}}" value="Mr." required="">
                                            <label class="form-check-label" for="inlineRadio0_{{$passangerTitleIndex}}">Mr.</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{$passengerInfoList['passengerInfo']['passengerType']}}[]" id="inlineRadio1_{{$passangerTitleIndex}}" value="Mrs.">
                                            <label class="form-check-label" for="inlineRadio1_{{$passangerTitleIndex}}">Mrs.</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{$passengerInfoList['passengerInfo']['passengerType']}}[]" id="inlineRadio2_{{$passangerTitleIndex}}" value="Ms.">
                                            <label class="form-check-label" for="inlineRadio2_{{$passangerTitleIndex}}">Ms.</label>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="form-row mt-3">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        First Name <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <div class="input-select position-relative">
                                            <input name="first_name[]" type="text" class="form-control" placeholder="First name" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Last Name <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <input name="last_name[]" type="text" class="form-control" placeholder="Last name" required="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Date of birth <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <input required="" class="form-control" type="date" placeholder="dd-mm-yyyy" name="dob[]" min="1900-01-01" max="{{date("Y-m-d")}}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Document Type <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <select name="document_type[]" class="form-select" required>
                                                    <option value="1" selected="">Passport</option>
                                                    <option value="2">National id</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input name="document_no[]" type="text" class="form-control" placeholder="Document Number" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Document expiration <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <input required="" name="document_expire_date[]" type="date" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <select required="" name="document_issue_country[]" class="form-select" aria-label="Default select example">
                                                    <option selected="" disabled="">--- Select Issue Country ---</option>
                                                    <option value="AFG">Afghan</option>
                                                    <option value="ALA">Åland Island</option>
                                                    <option value="ALB">Albanian</option>
                                                    <option value="DZA">Algerian</option>
                                                    <option value="ASM">American Samoan</option>
                                                    <option value="AND">Andorran</option>
                                                    <option value="AGO">Angolan</option>
                                                    <option value="AIA">Anguillan</option>
                                                    <option value="ATA">Antarctic</option>
                                                    <option value="ATG">Antiguan or Barbudan</option>
                                                    <option value="ARG">Argentine</option>
                                                    <option value="ARM">Armenian</option>
                                                    <option value="ABW">Aruban</option>
                                                    <option value="AUS">Australian</option>
                                                    <option value="AUT">Austrian</option>
                                                    <option value="AZE">Azerbaijani, Azeri</option>
                                                    <option value="BHS">Bahamian</option>
                                                    <option value="BHR">Bahraini</option>
                                                    <option value="BGD" selected="">Bangladeshi</option>
                                                    <option value="BRB">Barbadian</option>
                                                    <option value="BLR">Belarusian</option>
                                                    <option value="BEL">Belgian</option>
                                                    <option value="BLZ">Belizean</option>
                                                    <option value="BEN">Beninese, Beninois</option>
                                                    <option value="BMU">Bermudian, Bermudan</option>
                                                    <option value="BTN">Bhutanese</option>
                                                    <option value="BOL">Bolivian</option>
                                                    <option value="BES">Bonaire</option>
                                                    <option value="BIH">Bosnian or Herzegovinian</option>
                                                    <option value="BWA">Motswana, Botswanan</option>
                                                    <option value="BVT">Bouvet Island</option>
                                                    <option value="BRA">Brazilian</option>
                                                    <option value="IOT">BIOT</option>
                                                    <option value="BRN">Bruneian</option>
                                                    <option value="BGR">Bulgarian</option>
                                                    <option value="BFA">Burkinabé</option>
                                                    <option value="BDI">Burundian</option>
                                                    <option value="CPV">Cabo Verdean</option>
                                                    <option value="KHM">Cambodian</option>
                                                    <option value="CMR">Cameroonian</option>
                                                    <option value="CAN">Canadian</option>
                                                    <option value="CYM">Caymanian</option>
                                                    <option value="CAF">Central African</option>
                                                    <option value="TCD">Chadian</option>
                                                    <option value="CHL">Chilean</option>
                                                    <option value="CHN">Chinese</option>
                                                    <option value="CXR">Christmas Island</option>
                                                    <option value="CCK">Cocos Island</option>
                                                    <option value="COL">Colombian</option>
                                                    <option value="COM">Comoran, Comorian</option>
                                                    <option value="COG">Congolese</option>
                                                    <option value="COD">Congolese</option>
                                                    <option value="COK">Cook Island</option>
                                                    <option value="CRI">Costa Rican</option>
                                                    <option value="CIV">Ivorian</option>
                                                    <option value="HRV">Croatian</option>
                                                    <option value="CUB">Cuban</option>
                                                    <option value="CUW">Curaçaoan</option>
                                                    <option value="CYP">Cypriot</option>
                                                    <option value="CZE">Czech</option>
                                                    <option value="DNK">Danish</option>
                                                    <option value="DJI">Djiboutian</option>
                                                    <option value="DMA">Dominican</option>
                                                    <option value="DOM">Dominican</option>
                                                    <option value="ECU">Ecuadorian</option>
                                                    <option value="EGY">Egyptian</option>
                                                    <option value="SLV">Salvadoran</option>
                                                    <option value="GNQ">Equatorial Guinean, Equatoguinean</option>
                                                    <option value="ERI">Eritrean</option>
                                                    <option value="EST">Estonian</option>
                                                    <option value="ETH">Ethiopian</option>
                                                    <option value="FLK">Falkland Island</option>
                                                    <option value="FRO">Faroese</option>
                                                    <option value="FJI">Fijian</option>
                                                    <option value="FIN">Finnish</option>
                                                    <option value="FRA">French</option>
                                                    <option value="GUF">French Guianese</option>
                                                    <option value="PYF">French Polynesian</option>
                                                    <option value="ATF">French Southern Territories</option>
                                                    <option value="GAB">Gabonese</option>
                                                    <option value="GMB">Gambian</option>
                                                    <option value="GEO">Georgian</option>
                                                    <option value="DEU">German</option>
                                                    <option value="GHA">Ghanaian</option>
                                                    <option value="GIB">Gibraltar</option>
                                                    <option value="GRC">Greek, Hellenic</option>
                                                    <option value="GRL">Greenlandic</option>
                                                    <option value="GRD">Grenadian</option>
                                                    <option value="GLP">Guadeloupe</option>
                                                    <option value="GUM">Guamanian, Guambat</option>
                                                    <option value="GTM">Guatemalan</option>
                                                    <option value="GGY">Channel Island</option>
                                                    <option value="GIN">Guinean</option>
                                                    <option value="GNB">Bissau-Guinean</option>
                                                    <option value="GUY">Guyanese</option>
                                                    <option value="HTI">Haitian</option>
                                                    <option value="HMD">Heard Island or McDonald Islands</option>
                                                    <option value="VAT">Vatican</option>
                                                    <option value="HND">Honduran</option>
                                                    <option value="HKG">Hong Kong, Hong Kongese</option>
                                                    <option value="HUN">Hungarian, Magyar</option>
                                                    <option value="ISL">Icelandic</option>
                                                    <option value="IND">Indian</option>
                                                    <option value="IDN">Indonesian</option>
                                                    <option value="IRN">Iranian, Persian</option>
                                                    <option value="IRQ">Iraqi</option>
                                                    <option value="IRL">Irish</option>
                                                    <option value="IMN">Manx</option>
                                                    <option value="ISR">Israeli</option>
                                                    <option value="ITA">Italian</option>
                                                    <option value="JAM">Jamaican</option>
                                                    <option value="JPN">Japanese</option>
                                                    <option value="JEY">Channel Island</option>
                                                    <option value="JOR">Jordanian</option>
                                                    <option value="KAZ">Kazakhstani, Kazakh</option>
                                                    <option value="KEN">Kenyan</option>
                                                    <option value="KIR">I-Kiribati</option>
                                                    <option value="PRK">North Korean</option>
                                                    <option value="KOR">South Korean</option>
                                                    <option value="KWT">Kuwaiti</option>
                                                    <option value="KGZ">Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz</option>
                                                    <option value="LAO">Lao, Laotian</option>
                                                    <option value="LVA">Latvian</option>
                                                    <option value="LBN">Lebanese</option>
                                                    <option value="LSO">Basotho</option>
                                                    <option value="LBR">Liberian</option>
                                                    <option value="LBY">Libyan</option>
                                                    <option value="LIE">Liechtenstein</option>
                                                    <option value="LTU">Lithuanian</option>
                                                    <option value="LUX">Luxembourg, Luxembourgish</option>
                                                    <option value="MAC">Macanese, Chinese</option>
                                                    <option value="MKD">Macedonian</option>
                                                    <option value="MDG">Malagasy</option>
                                                    <option value="MWI">Malawian</option>
                                                    <option value="MYS">Malaysian</option>
                                                    <option value="MDV">Maldivian</option>
                                                    <option value="MLI">Malian, Malinese</option>
                                                    <option value="MLT">Maltese</option>
                                                    <option value="MHL">Marshallese</option>
                                                    <option value="MTQ">Martiniquais, Martinican</option>
                                                    <option value="MRT">Mauritanian</option>
                                                    <option value="MUS">Mauritian</option>
                                                    <option value="MYT">Mahoran</option>
                                                    <option value="MEX">Mexican</option>
                                                    <option value="FSM">Micronesian</option>
                                                    <option value="MDA">Moldovan</option>
                                                    <option value="MCO">Monégasque, Monacan</option>
                                                    <option value="MNG">Mongolian</option>
                                                    <option value="MNE">Montenegrin</option>
                                                    <option value="MSR">Montserratian</option>
                                                    <option value="MAR">Moroccan</option>
                                                    <option value="MOZ">Mozambican</option>
                                                    <option value="MMR">Burmese</option>
                                                    <option value="NAM">Namibian</option>
                                                    <option value="NRU">Nauruan</option>
                                                    <option value="NPL">Nepali, Nepalese</option>
                                                    <option value="NLD">Dutch, Netherlandic</option>
                                                    <option value="NCL">New Caledonian</option>
                                                    <option value="NZL">New Zealand, NZ</option>
                                                    <option value="NIC">Nicaraguan</option>
                                                    <option value="NER">Nigerien</option>
                                                    <option value="NGA">Nigerian</option>
                                                    <option value="NIU">Niuean</option>
                                                    <option value="NFK">Norfolk Island</option>
                                                    <option value="MNP">Northern Marianan</option>
                                                    <option value="NOR">Norwegian</option>
                                                    <option value="OMN">Omani</option>
                                                    <option value="PAK">Pakistani</option>
                                                    <option value="PLW">Palauan</option>
                                                    <option value="PSE">Palestinian</option>
                                                    <option value="PAN">Panamanian</option>
                                                    <option value="PNG">Papua New Guinean, Papuan</option>
                                                    <option value="PRY">Paraguayan</option>
                                                    <option value="PER">Peruvian</option>
                                                    <option value="PHL">Philippine, Filipino</option>
                                                    <option value="PCN">Pitcairn Island</option>
                                                    <option value="POL">Polish</option>
                                                    <option value="PRT">Portuguese</option>
                                                    <option value="PRI">Puerto Rican</option>
                                                    <option value="QAT">Qatari</option>
                                                    <option value="REU">Réunionese, Réunionnais</option>
                                                    <option value="ROU">Romanian</option>
                                                    <option value="RUS">Russian</option>
                                                    <option value="RWA">Rwandan</option>
                                                    <option value="BLM">Barthélemois</option>
                                                    <option value="SHN">Saint Helenian</option>
                                                    <option value="KNA">Kittitian or Nevisian</option>
                                                    <option value="LCA">Saint Lucian</option>
                                                    <option value="MAF">Saint-Martinoise</option>
                                                    <option value="SPM">Saint-Pierrais or Miquelonnais</option>
                                                    <option value="VCT">Saint Vincentian, Vincentian</option>
                                                    <option value="WSM">Samoan</option>
                                                    <option value="SMR">Sammarinese</option>
                                                    <option value="STP">São Toméan</option>
                                                    <option value="SAU">Saudi, Saudi Arabian</option>
                                                    <option value="SEN">Senegalese</option>
                                                    <option value="SRB">Serbian</option>
                                                    <option value="SYC">Seychellois</option>
                                                    <option value="SLE">Sierra Leonean</option>
                                                    <option value="SGP">Singaporean</option>
                                                    <option value="SXM">Sint Maarten</option>
                                                    <option value="SVK">Slovak</option>
                                                    <option value="SVN">Slovenian, Slovene</option>
                                                    <option value="SLB">Solomon Island</option>
                                                    <option value="SOM">Somali, Somalian</option>
                                                    <option value="ZAF">South African</option>
                                                    <option value="SGS">South Georgia or South Sandwich Islands</option>
                                                    <option value="SSD">South Sudanese</option>
                                                    <option value="ESP">Spanish</option>
                                                    <option value="LKA">Sri Lankan</option>
                                                    <option value="SDN">Sudanese</option>
                                                    <option value="SUR">Surinamese</option>
                                                    <option value="SJM">Svalbard</option>
                                                    <option value="SWZ">Swazi</option>
                                                    <option value="SWE">Swedish</option>
                                                    <option value="CHE">Swiss</option>
                                                    <option value="SYR">Syrian</option>
                                                    <option value="TWN">Chinese, Taiwanese</option>
                                                    <option value="TJK">Tajikistani</option>
                                                    <option value="TZA">Tanzanian</option>
                                                    <option value="THA">Thai</option>
                                                    <option value="TLS">Timorese</option>
                                                    <option value="TGO">Togolese</option>
                                                    <option value="TKL">Tokelauan</option>
                                                    <option value="TON">Tongan</option>
                                                    <option value="TTO">Trinidadian or Tobagonian</option>
                                                    <option value="TUN">Tunisian</option>
                                                    <option value="TUR">Turkish</option>
                                                    <option value="TKM">Turkmen</option>
                                                    <option value="TCA">Turks and Caicos Island</option>
                                                    <option value="TUV">Tuvaluan</option>
                                                    <option value="UGA">Ugandan</option>
                                                    <option value="UKR">Ukrainian</option>
                                                    <option value="ARE">Emirati, Emirian, Emiri</option>
                                                    <option value="GBR">British, UK</option>
                                                    <option value="UMI">American</option>
                                                    <option value="USA">American</option>
                                                    <option value="URY">Uruguayan</option>
                                                    <option value="UZB">Uzbekistani, Uzbek</option>
                                                    <option value="VUT">Ni-Vanuatu, Vanuatuan</option>
                                                    <option value="VEN">Venezuelan</option>
                                                    <option value="VNM">Vietnamese</option>
                                                    <option value="VGB">British Virgin Island</option>
                                                    <option value="VIR">U.S. Virgin Island</option>
                                                    <option value="WLF">Wallis and Futuna, Wallisian or Futunan</option>
                                                    <option value="ESH">Sahrawi, Sahrawian, Sahraouian</option>
                                                    <option value="YEM">Yemeni</option>
                                                    <option value="ZMB">Zambian</option>
                                                    <option value="ZWE">Zimbabwean</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-12 col-md-3 font-weight-500 text-left text-md-right mb-3 mb-md-0 pr-3 px-3">
                                        Nationality <span class="text-danger">*</span>
                                    </div>
                                    <div class="col-12 col-md-8 mb-3 mb-sm-3">
                                        <div class="input-select position-relative">
                                            <select required="" name="nationality[]" class="form-select" aria-label="Default select example">
                                                <option selected="" disabled="">---------- Please Select Nationality ----------</option>
                                                <option value="AFG">Afghan</option>
                                                <option value="ALA">Åland Island</option>
                                                <option value="ALB">Albanian</option>
                                                <option value="DZA">Algerian</option>
                                                <option value="ASM">American Samoan</option>
                                                <option value="AND">Andorran</option>
                                                <option value="AGO">Angolan</option>
                                                <option value="AIA">Anguillan</option>
                                                <option value="ATA">Antarctic</option>
                                                <option value="ATG">Antiguan or Barbudan</option>
                                                <option value="ARG">Argentine</option>
                                                <option value="ARM">Armenian</option>
                                                <option value="ABW">Aruban</option>
                                                <option value="AUS">Australian</option>
                                                <option value="AUT">Austrian</option>
                                                <option value="AZE">Azerbaijani, Azeri</option>
                                                <option value="BHS">Bahamian</option>
                                                <option value="BHR">Bahraini</option>
                                                <option value="BGD" selected="">Bangladeshi</option>
                                                <option value="BRB">Barbadian</option>
                                                <option value="BLR">Belarusian</option>
                                                <option value="BEL">Belgian</option>
                                                <option value="BLZ">Belizean</option>
                                                <option value="BEN">Beninese, Beninois</option>
                                                <option value="BMU">Bermudian, Bermudan</option>
                                                <option value="BTN">Bhutanese</option>
                                                <option value="BOL">Bolivian</option>
                                                <option value="BES">Bonaire</option>
                                                <option value="BIH">Bosnian or Herzegovinian</option>
                                                <option value="BWA">Motswana, Botswanan</option>
                                                <option value="BVT">Bouvet Island</option>
                                                <option value="BRA">Brazilian</option>
                                                <option value="IOT">BIOT</option>
                                                <option value="BRN">Bruneian</option>
                                                <option value="BGR">Bulgarian</option>
                                                <option value="BFA">Burkinabé</option>
                                                <option value="BDI">Burundian</option>
                                                <option value="CPV">Cabo Verdean</option>
                                                <option value="KHM">Cambodian</option>
                                                <option value="CMR">Cameroonian</option>
                                                <option value="CAN">Canadian</option>
                                                <option value="CYM">Caymanian</option>
                                                <option value="CAF">Central African</option>
                                                <option value="TCD">Chadian</option>
                                                <option value="CHL">Chilean</option>
                                                <option value="CHN">Chinese</option>
                                                <option value="CXR">Christmas Island</option>
                                                <option value="CCK">Cocos Island</option>
                                                <option value="COL">Colombian</option>
                                                <option value="COM">Comoran, Comorian</option>
                                                <option value="COG">Congolese</option>
                                                <option value="COD">Congolese</option>
                                                <option value="COK">Cook Island</option>
                                                <option value="CRI">Costa Rican</option>
                                                <option value="CIV">Ivorian</option>
                                                <option value="HRV">Croatian</option>
                                                <option value="CUB">Cuban</option>
                                                <option value="CUW">Curaçaoan</option>
                                                <option value="CYP">Cypriot</option>
                                                <option value="CZE">Czech</option>
                                                <option value="DNK">Danish</option>
                                                <option value="DJI">Djiboutian</option>
                                                <option value="DMA">Dominican</option>
                                                <option value="DOM">Dominican</option>
                                                <option value="ECU">Ecuadorian</option>
                                                <option value="EGY">Egyptian</option>
                                                <option value="SLV">Salvadoran</option>
                                                <option value="GNQ">Equatorial Guinean, Equatoguinean</option>
                                                <option value="ERI">Eritrean</option>
                                                <option value="EST">Estonian</option>
                                                <option value="ETH">Ethiopian</option>
                                                <option value="FLK">Falkland Island</option>
                                                <option value="FRO">Faroese</option>
                                                <option value="FJI">Fijian</option>
                                                <option value="FIN">Finnish</option>
                                                <option value="FRA">French</option>
                                                <option value="GUF">French Guianese</option>
                                                <option value="PYF">French Polynesian</option>
                                                <option value="ATF">French Southern Territories</option>
                                                <option value="GAB">Gabonese</option>
                                                <option value="GMB">Gambian</option>
                                                <option value="GEO">Georgian</option>
                                                <option value="DEU">German</option>
                                                <option value="GHA">Ghanaian</option>
                                                <option value="GIB">Gibraltar</option>
                                                <option value="GRC">Greek, Hellenic</option>
                                                <option value="GRL">Greenlandic</option>
                                                <option value="GRD">Grenadian</option>
                                                <option value="GLP">Guadeloupe</option>
                                                <option value="GUM">Guamanian, Guambat</option>
                                                <option value="GTM">Guatemalan</option>
                                                <option value="GGY">Channel Island</option>
                                                <option value="GIN">Guinean</option>
                                                <option value="GNB">Bissau-Guinean</option>
                                                <option value="GUY">Guyanese</option>
                                                <option value="HTI">Haitian</option>
                                                <option value="HMD">Heard Island or McDonald Islands</option>
                                                <option value="VAT">Vatican</option>
                                                <option value="HND">Honduran</option>
                                                <option value="HKG">Hong Kong, Hong Kongese</option>
                                                <option value="HUN">Hungarian, Magyar</option>
                                                <option value="ISL">Icelandic</option>
                                                <option value="IND">Indian</option>
                                                <option value="IDN">Indonesian</option>
                                                <option value="IRN">Iranian, Persian</option>
                                                <option value="IRQ">Iraqi</option>
                                                <option value="IRL">Irish</option>
                                                <option value="IMN">Manx</option>
                                                <option value="ISR">Israeli</option>
                                                <option value="ITA">Italian</option>
                                                <option value="JAM">Jamaican</option>
                                                <option value="JPN">Japanese</option>
                                                <option value="JEY">Channel Island</option>
                                                <option value="JOR">Jordanian</option>
                                                <option value="KAZ">Kazakhstani, Kazakh</option>
                                                <option value="KEN">Kenyan</option>
                                                <option value="KIR">I-Kiribati</option>
                                                <option value="PRK">North Korean</option>
                                                <option value="KOR">South Korean</option>
                                                <option value="KWT">Kuwaiti</option>
                                                <option value="KGZ">Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz</option>
                                                <option value="LAO">Lao, Laotian</option>
                                                <option value="LVA">Latvian</option>
                                                <option value="LBN">Lebanese</option>
                                                <option value="LSO">Basotho</option>
                                                <option value="LBR">Liberian</option>
                                                <option value="LBY">Libyan</option>
                                                <option value="LIE">Liechtenstein</option>
                                                <option value="LTU">Lithuanian</option>
                                                <option value="LUX">Luxembourg, Luxembourgish</option>
                                                <option value="MAC">Macanese, Chinese</option>
                                                <option value="MKD">Macedonian</option>
                                                <option value="MDG">Malagasy</option>
                                                <option value="MWI">Malawian</option>
                                                <option value="MYS">Malaysian</option>
                                                <option value="MDV">Maldivian</option>
                                                <option value="MLI">Malian, Malinese</option>
                                                <option value="MLT">Maltese</option>
                                                <option value="MHL">Marshallese</option>
                                                <option value="MTQ">Martiniquais, Martinican</option>
                                                <option value="MRT">Mauritanian</option>
                                                <option value="MUS">Mauritian</option>
                                                <option value="MYT">Mahoran</option>
                                                <option value="MEX">Mexican</option>
                                                <option value="FSM">Micronesian</option>
                                                <option value="MDA">Moldovan</option>
                                                <option value="MCO">Monégasque, Monacan</option>
                                                <option value="MNG">Mongolian</option>
                                                <option value="MNE">Montenegrin</option>
                                                <option value="MSR">Montserratian</option>
                                                <option value="MAR">Moroccan</option>
                                                <option value="MOZ">Mozambican</option>
                                                <option value="MMR">Burmese</option>
                                                <option value="NAM">Namibian</option>
                                                <option value="NRU">Nauruan</option>
                                                <option value="NPL">Nepali, Nepalese</option>
                                                <option value="NLD">Dutch, Netherlandic</option>
                                                <option value="NCL">New Caledonian</option>
                                                <option value="NZL">New Zealand, NZ</option>
                                                <option value="NIC">Nicaraguan</option>
                                                <option value="NER">Nigerien</option>
                                                <option value="NGA">Nigerian</option>
                                                <option value="NIU">Niuean</option>
                                                <option value="NFK">Norfolk Island</option>
                                                <option value="MNP">Northern Marianan</option>
                                                <option value="NOR">Norwegian</option>
                                                <option value="OMN">Omani</option>
                                                <option value="PAK">Pakistani</option>
                                                <option value="PLW">Palauan</option>
                                                <option value="PSE">Palestinian</option>
                                                <option value="PAN">Panamanian</option>
                                                <option value="PNG">Papua New Guinean, Papuan</option>
                                                <option value="PRY">Paraguayan</option>
                                                <option value="PER">Peruvian</option>
                                                <option value="PHL">Philippine, Filipino</option>
                                                <option value="PCN">Pitcairn Island</option>
                                                <option value="POL">Polish</option>
                                                <option value="PRT">Portuguese</option>
                                                <option value="PRI">Puerto Rican</option>
                                                <option value="QAT">Qatari</option>
                                                <option value="REU">Réunionese, Réunionnais</option>
                                                <option value="ROU">Romanian</option>
                                                <option value="RUS">Russian</option>
                                                <option value="RWA">Rwandan</option>
                                                <option value="BLM">Barthélemois</option>
                                                <option value="SHN">Saint Helenian</option>
                                                <option value="KNA">Kittitian or Nevisian</option>
                                                <option value="LCA">Saint Lucian</option>
                                                <option value="MAF">Saint-Martinoise</option>
                                                <option value="SPM">Saint-Pierrais or Miquelonnais</option>
                                                <option value="VCT">Saint Vincentian, Vincentian</option>
                                                <option value="WSM">Samoan</option>
                                                <option value="SMR">Sammarinese</option>
                                                <option value="STP">São Toméan</option>
                                                <option value="SAU">Saudi, Saudi Arabian</option>
                                                <option value="SEN">Senegalese</option>
                                                <option value="SRB">Serbian</option>
                                                <option value="SYC">Seychellois</option>
                                                <option value="SLE">Sierra Leonean</option>
                                                <option value="SGP">Singaporean</option>
                                                <option value="SXM">Sint Maarten</option>
                                                <option value="SVK">Slovak</option>
                                                <option value="SVN">Slovenian, Slovene</option>
                                                <option value="SLB">Solomon Island</option>
                                                <option value="SOM">Somali, Somalian</option>
                                                <option value="ZAF">South African</option>
                                                <option value="SGS">South Georgia or South Sandwich Islands</option>
                                                <option value="SSD">South Sudanese</option>
                                                <option value="ESP">Spanish</option>
                                                <option value="LKA">Sri Lankan</option>
                                                <option value="SDN">Sudanese</option>
                                                <option value="SUR">Surinamese</option>
                                                <option value="SJM">Svalbard</option>
                                                <option value="SWZ">Swazi</option>
                                                <option value="SWE">Swedish</option>
                                                <option value="CHE">Swiss</option>
                                                <option value="SYR">Syrian</option>
                                                <option value="TWN">Chinese, Taiwanese</option>
                                                <option value="TJK">Tajikistani</option>
                                                <option value="TZA">Tanzanian</option>
                                                <option value="THA">Thai</option>
                                                <option value="TLS">Timorese</option>
                                                <option value="TGO">Togolese</option>
                                                <option value="TKL">Tokelauan</option>
                                                <option value="TON">Tongan</option>
                                                <option value="TTO">Trinidadian or Tobagonian</option>
                                                <option value="TUN">Tunisian</option>
                                                <option value="TUR">Turkish</option>
                                                <option value="TKM">Turkmen</option>
                                                <option value="TCA">Turks and Caicos Island</option>
                                                <option value="TUV">Tuvaluan</option>
                                                <option value="UGA">Ugandan</option>
                                                <option value="UKR">Ukrainian</option>
                                                <option value="ARE">Emirati, Emirian, Emiri</option>
                                                <option value="GBR">British, UK</option>
                                                <option value="UMI">American</option>
                                                <option value="USA">American</option>
                                                <option value="URY">Uruguayan</option>
                                                <option value="UZB">Uzbekistani, Uzbek</option>
                                                <option value="VUT">Ni-Vanuatu, Vanuatuan</option>
                                                <option value="VEN">Venezuelan</option>
                                                <option value="VNM">Vietnamese</option>
                                                <option value="VGB">British Virgin Island</option>
                                                <option value="VIR">U.S. Virgin Island</option>
                                                <option value="WLF">Wallis and Futuna, Wallisian or Futunan</option>
                                                <option value="ESH">Sahrawi, Sahrawian, Sahraouian</option>
                                                <option value="YEM">Yemeni</option>
                                                <option value="ZMB">Zambian</option>
                                                <option value="ZWE">Zimbabwean</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @php $passangerTitleIndex++; @endphp
                                @endfor
                            @endforeach

                            <hr>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Make booking</button>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="card shadow border-0 mb-3">
                    <div class="card-body py-5 px-5">
                        <div class="fs-14 mb-0 ">
                            <h5 class="fw-bold">Mandatory check list for passengers</h5>
                            <ul class="list-style ps-3">
                                <li>Certify your health status through the aarogya setu app or the self declaration form</li>
                                <li>Remember to do web check in before arriving at the airport please do carry an e boarding pass on your mobile alternatively you can carry the printout of the boarding pass</li>
                                <li>Please reach at least 2 hours prior to flight departure</li>
                                <li>No meal service will be available on board</li>
                                <li>Face masks are compulsory we urge you to carry your own</li>
                                <li>You are requested to print and paste the baggage tag attached to your booking confirmation alternatively you can write your name pnr and flight number on an a4 sheet and affix on your bag</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 rightSidebar">
            {{-- pricing info start --}}
            <div class="theiaStickySidebar">
                <div class="card shadow border-0 mb-3 d-none d-xl-block">
                    <div class="card-body">
                        <h3 class="fs-17 mb-0">Fare summary</h3>
                        <p class="fs-14">Travellers :
                            @foreach ($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'] as $passengerData)
                                <b>
                                    {{ $passengerData['passengerInfo']['passengerNumber'] }}
                                    {{ $passengerData['passengerInfo']['passengerType'] }}
                                </b>&nbsp;
                            @endforeach
                        </p>
                        <hr>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="summary-text">
                                <div class="font-weight-500">Base Fare</div>
                            </div>
                            <div class="fs-16 font-weight-500" style="font-weight: 600;">
                                ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})

                                @if($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareCurrency'] == 'USD')
                                    {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareAmount'] * $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['currencyConversion']['exchangeRateUsed'])}}
                                @else
                                    {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['baseFareAmount']) }}
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="summary-text">
                                <div class="font-weight-500"> Total Tax Amount </div>
                            </div>
                            <div class="fs-16 font-weight-500" style="font-weight: 600;">
                                ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalTaxAmount']) }}
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="">
                                <div class="fs-14 font-weight-300">Total Payable Amount</div>
                            </div>
                            <div class="fs-16 font-weight-500">
                                <span class="ml-2 text-primary" style="font-weight: 600;">
                                    ({{ $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['currency'] }})
                                    {{ number_format($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['totalFare']['totalPrice']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- pricing info end --}}
        </div>
    </div>
@endsection
