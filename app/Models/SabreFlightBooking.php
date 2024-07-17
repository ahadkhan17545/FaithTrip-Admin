<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SabreGdsConfig;

class SabreFlightBooking extends Model
{
    use HasFactory;

    public static function flightBooking($revlidatedData, $travellerContact, $travellerName, $travellerEmail){

        $passengerTypes = array();
        if (session('adult') > 0) {
            $passengerTypes[] = array("Code" => "ADT", "Quantity" => (string) session('adult'));
        }
        if (session('child') > 0) {
            $passengerTypes[] = array("Code" => "CNN", "Quantity" => (string) session('child'));
        }
        if (session('infant') > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (string) session('infant'));
        }


        // making flight segment start

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

        $flightSegment = array();
        $arrivalDateTimeArray = array();
        foreach ($segmentArray as $segmentIndex => $segmentData){

            // modify departure date if the date change
            if($segmentIndex == 0) {
                $departureDateTime = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($segmentData['departure']['time'], 0, 8);
                $elapsedDate = date('Y-m-d', strtotime($departureDateTime . ' +'.$segmentData['elapsedTime'].' minutes'));
                $arrivalDateTime = $elapsedDate."T".substr($segmentData['arrival']['time'], 0, 8);
            } else{
                $time1 = substr($segmentData['departure']['time'],0,8);
                $time2 = substr($segmentArray[$segmentIndex-1]['arrival']['time'],0,8);
                $time1Obj = DateTime::createFromFormat('H:i:s', $time1);
                $time2Obj = DateTime::createFromFormat('H:i:s', $time2);
                $interval = $time1Obj->diff($time2Obj);
                $totalMinutes = ($interval->h * 60) + $interval->i;

                // checking its a return flight or
                if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]) && $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureLocation'] == $segmentData['departure']['airport']){
                    $departureDateTime = date('Y-m-d', strtotime($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']))."T".substr($segmentData['departure']['time'], 0, 8);
                } else {
                    $departureDateTime = date('Y-m-d', strtotime($arrivalDateTimeArray[$segmentIndex-1] . ' +'.$totalMinutes.' minutes'))."T".substr($segmentData['departure']['time'], 0, 8);
                }

                $elapsedDate = date('Y-m-d', strtotime($departureDateTime . ' +'.$segmentData['elapsedTime'].' minutes'));
                $arrivalDateTime = $elapsedDate."T".substr($segmentData['arrival']['time'], 0, 8);
            }
            $arrivalDateTimeArray[] = $arrivalDateTime;


            $flightSegment[] = array(
                "DepartureDateTime" => $departureDateTime,
                "FlightNumber" => (string) $segmentData['carrier']['operatingFlightNumber'],
                "NumberInParty" => (string) 1,
                "ResBookDesigCode" => "Y",
                "Status" => "NN",
                "DestinationLocation" => array("LocationCode" => $segmentData['arrival']['airport']),
                "MarketingAirline" => array("Code" => $segmentData['carrier']['marketing'], "FlightNumber" => (string) $segmentData['carrier']['marketingFlightNumber']),
                "MarriageGrp" => "O",
                "OriginLocation" => array("LocationCode" => $segmentData['departure']['airport'])
            );
        }
        // making flight segment end


        $givenName = explode(" ",$travellerName)[0];
        $surName = explode(" ",$travellerName);
        $surName = end($surName);

        $request_body = array(
            "CreatePassengerNameRecordRQ" => array(
                "version" => "2.5.0",
                "targetCity" => "S00L",
                "haltOnAirPriceError" => true,
                "TravelItineraryAddInfo" => array(
                    "AgencyInfo" => array(
                        "Address" => array(
                            "AddressLine" => "SABRE TRAVEL",
                            "CityName" => "SOUTHLAKE",
                            "CountryCode" => "US",
                            "PostalCode" => "76092",
                            "StateCountyProv" => array(
                                "StateCode" => "TX"
                            ),
                            "StreetNmbr" => "3150 SABRE DRIVE"
                        ),
                        "Ticketing" => array(
                            "TicketType" => "7TAW"
                        )
                    ),
                    "CustomerInfo" => array(
                        "ContactNumbers" => array(
                            "ContactNumber" => array(
                                array(
                                    "NameNumber" => "1.1",
                                    "Phone" => $travellerContact,
                                    "PhoneUseType" => "H"
                                )
                            )
                        ),
                        "CreditCardData" => array(
                            "PreferredCustomer" => array(
                                "ind" => true
                            )
                        ),
                        "PersonName" => array(
                            array(
                                "NameNumber" => "1.1",
                                "PassengerType" => "ADT",
                                "GivenName" => $givenName,
                                "Surname" => $surName
                            )
                        ),
                        "Email" => array(
                            array(
                                "Address" => $travellerEmail,
                                "Type" => "BC"
                            )
                        )
                    )
                ),
                "AirBook" => array(
                    "HaltOnStatus" => array(
                        array("Code" => "HL"),
                        array("Code" => "KK"),
                        array("Code" => "LL"),
                        array("Code" => "NN"),
                        array("Code" => "NO"),
                        array("Code" => "UC"),
                        array("Code" => "US")
                    ),
                    "OriginDestinationInformation" => array(
                        "FlightSegment" => $flightSegment
                    ),
                    "RedisplayReservation" => array(
                        "NumAttempts" => 3,
                        "WaitInterval" => 1000
                    )
                ),
                "AirPrice" => array(
                    array(
                        "PriceRequestInformation" => array(
                            "Retain" => true,
                            "OptionalQualifiers" => array(
                                "PricingQualifiers" => array(
                                    "NameSelect" => array(
                                        array("NameNumber" => "1.1")
                                    ),
                                    "PassengerType" => $passengerTypes
                                )
                            )
                        )
                    )
                ),
                // "SpecialReqDetails" => array(
                //     "AddRemark" => array(
                //         "RemarkInfo" => array(
                //             "Remark" => array(
                //                 array("Type" => "General", "Text" => "WDF100433"),
                //                 array("Type" => "Historical", "Text" => "TEST01"),
                //                 array("Type" => "Client Address", "Text" => "3399 CURE AVE 76554 GALLUP TX"),
                //                 array("Type" => "Invoice", "Text" => "S*UD18 PROMO515")
                //             )
                //         )
                //     ),
                //     "SpecialService" => array(
                //         "SpecialServiceInfo" => array(
                //             "SecureFlight" => array(
                //                 array(
                //                     "SegmentNumber" => "A",
                //                     "PersonName" => array(
                //                         "DateOfBirth" => "1989-01-01",
                //                         "Gender" => "M",
                //                         "NameNumber" => "1.1",
                //                         "GivenName" => "JOE",
                //                         "Surname" => "DOE"
                //                     )
                //                 )
                //             ),
                //             "Service" => array(
                //                 array(
                //                     "SSR_Code" => "CTCE",
                //                     "SegmentNumber" => "A",
                //                     "Text" => "ADMIN//CURE.NET",
                //                     "PersonName" => array("NameNumber" => "1.1")
                //                 ),
                //                 array(
                //                     "SSR_Code" => "CTCM",
                //                     "Text" => "5551231234",
                //                     "PersonName" => array("NameNumber" => "1.1")
                //                 )
                //             )
                //         )
                //     )
                // ),
                "PostProcessing" => array(
                    "RedisplayReservation" => array("waitInterval" => 100),
                    "EndTransaction" => array(
                        "Source" => array("ReceivedFrom" => "API TEST")
                    )
                )
            )
        );


        // Convert the request body array to JSON format
        $request_json = json_encode($request_body);

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v2.5.0/passenger/records?mode=create';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v2.5.0/passenger/records?mode=create';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiEndPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $request_json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Conversation-ID: 2021.01.DevStudio',
                'Authorization: Bearer  '. session('access_token'),
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        // return $request_json;
        // return $flightSegment;

    }
}
