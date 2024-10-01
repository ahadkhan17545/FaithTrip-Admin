<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SabreGdsConfig;

class SabreFlightBooking extends Model
{
    use HasFactory;
    public static function flightBooking($revlidatedData, $travellerContact, $travellerEmail, $firstNames, $lastNames, $passangerTitles, $dob, $passangerTypes, $ages, $documentIssueCountry, $nationality, $documentNo, $documentExpireDate){

        // $passengerTypes = array();
        // if (session('adult') > 0) {
        //     $passengerTypes[] = array("Code" => "ADT", "Quantity" => (string) session('adult'));
        // }
        // if (session('child') > 0) {
        //     $passengerTypes[] = array("Code" => "CNN", "Quantity" => (string) session('child'));
        // }
        // if (session('infant') > 0) {
        //     $passengerTypes[] = array("Code" => "INF", "Quantity" => (string) session('infant'));
        // }


        // making flight segment start
        $segmentArray = [];
        $legsArray = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['legs'];
        foreach ($legsArray as $key => $leg) {
            $legRef = $leg['ref'] - 1;
            $legDescription = $revlidatedData['groupedItineraryResponse']['legDescs'][$legRef];
            $schedulesArray = $legDescription['schedules'];
            foreach ($schedulesArray as $schedulesArrayIndex => $schedule) {
                $scheduleRef = $schedule['ref'] - 1;
                $segmentArray[] = $revlidatedData['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
                if(isset($schedule['departureDateAdjustment'])){
                    $segmentArray[$schedulesArrayIndex]['bothDateAdjustment'] = $schedule['departureDateAdjustment'];
                }
            }
        }

        $flightSegment = array();
        $departureDate = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'];
        $returnDepartureDate = isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']) ? $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate'] : null;
        $isReturnFlight = false;

        foreach ($segmentArray as $segmentIndex => $segmentData){

            // Check if this is a return flight segment
            if ($isReturnFlight == false && $returnDepartureDate && $segmentData['departure']['airport'] == $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureLocation']) {
                $isReturnFlight = true;
            }

            if($isReturnFlight == false){
                $departureDateTime = new DateTime($departureDate . ' ' . $segmentData['departure']['time']);
            } else {
                $departureDateTime = new DateTime($returnDepartureDate . ' ' . $segmentData['departure']['time']);
            }

            if(isset($segmentData['bothDateAdjustment']) && $segmentData['bothDateAdjustment'] >= 1){
                $departureDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
            } else {
                // Adjust the departure date if there's a date adjustment only for departure
                if (isset($segmentData['departure']['dateAdjustment']) && $segmentData['departure']['dateAdjustment'] > 0) {
                    $departureDateTime->modify('+' . $segmentData['departure']['dateAdjustment'] . ' day');
                }
            }


            $bookingCode = $revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['bookingCode'] ?? "L";

            $marriageGrp = "O";
            if(isset($revlidatedData['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][0]['pricingInformation'][0]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$segmentIndex]['segment']['availabilityBreak'])){
                $marriageGrp = "I";
            }

            $flightSegment[] = array(
                "DepartureDateTime" => $departureDateTime->format('Y-m-d')."T".$departureDateTime->format('H:i:s'),
                "FlightNumber" => (string) $segmentData['carrier']['operatingFlightNumber'],
                "NumberInParty" => (string) (session('adult')+session('child')+session('infant')),
                "ResBookDesigCode" => (string) $bookingCode,
                "Status" => "NN",
                "OriginLocation" => array(
                    "LocationCode" => $segmentData['departure']['airport']
                ),
                "DestinationLocation" => array(
                    "LocationCode" => $segmentData['arrival']['airport']
                ),
                "MarketingAirline" => array(
                    "Code" => $segmentData['carrier']['marketing'],
                    "FlightNumber" => (string) $segmentData['carrier']['marketingFlightNumber']
                ),
                "MarriageGrp" => $marriageGrp
            );
        }
        // making flight segment end


        $personName = [];
        $pricingQualifiersPassengerTypes = [];
        $advancePassengers = [];
        $secureFlights = [];

        $specialServices = [];
        $specialServices[] = [
            "SSR_Code" => "CTCM",
            "Text" => (string) $travellerContact,
            "PersonName" => [
                "NameNumber" => "1.1"
            ],
            "SegmentNumber" => "A"
        ];
        $specialServices[] = [
            "SSR_Code" => "CTCE",
            "Text" => (string) str_replace("@","//",$travellerEmail),
            "PersonName" => [
                "NameNumber" => "1.1"
            ],
            "SegmentNumber" => "A"
        ];

        foreach($firstNames as $passangerIndex => $firstName){

            $nameReference = "";
            if($passangerTypes[$passangerIndex] != "ADT"){
                if($passangerTypes[$passangerIndex] == 'INF'){
                    $nameReference = 'I'.str_pad($ages[$passangerIndex],2,"0",STR_PAD_LEFT);
                } else {
                    $nameReference = 'C'.str_pad($ages[$passangerIndex],2,"0",STR_PAD_LEFT);
                }
            }

            $passengerTypeForPersonName = "ADT";
            if($passangerTypes[$passangerIndex] != "ADT"){
                if($passangerTypes[$passangerIndex] == 'INF'){
                    $passengerTypeForPersonName = "INF";

                    $specialServices[] = [
                        "SSR_Code" => "INFT",
                        "Text" => str_replace(" ","/",str_replace(".","",$passangerTitles[$passangerIndex])."/".trim($firstName))."/".str_replace(" ","/",trim($lastNames[$passangerIndex]))." /".date("dMy", strtotime($dob[$passangerIndex])),
                        "PersonName" => [
                            "NameNumber" => (string) $passangerIndex+1 .".1"
                        ],
                        "SegmentNumber" => "A"
                    ];

                } else{
                    $passengerTypeForPersonName = 'C'.str_pad($ages[$passangerIndex],2,"0",STR_PAD_LEFT);

                    $specialServices[] = [
                        "SSR_Code" => "CHLD",
                        "Text" => (string) date("dMy", strtotime($dob[$passangerIndex])),
                        "PersonName" => [
                            "NameNumber" => (string) $passangerIndex+1 .".1"
                        ],
                        "SegmentNumber" => "A"
                    ];
                }
            }

            $personName[] = [
                "GivenName" => str_replace(".","",$passangerTitles[$passangerIndex])." ".$firstName,
                "Surname" => $lastNames[$passangerIndex],
                "NameNumber" => (string) $passangerIndex+1 .".1",
                "Infant" => $passangerTypes[$passangerIndex] == 'INF' ? true : false,
                "NameReference" => $nameReference,
                "PassengerType" => $passengerTypeForPersonName,
            ];

            $advancePassengers[] = [
                "Document" => [
                    'IssueCountry' => $documentIssueCountry[$passangerIndex],
                    'NationalityCountry' => $nationality[$passangerIndex],
                    'ExpirationDate' => (string) $documentExpireDate[$passangerIndex],
                    'Number' => (string) $documentNo[$passangerIndex],
                    'Type' => "P",
                ],
                "PersonName" => [
                    'Gender' => ($passangerTitles[$passangerIndex] == 'Mr.' || $passangerTitles[$passangerIndex] == 'Mstr.') ? "M" : "F",
                    'GivenName' => str_replace(".","",$passangerTitles[$passangerIndex])." ".$firstName,
                    'Surname' => $lastNames[$passangerIndex],
                    'DateOfBirth' => (string) $dob[$passangerIndex],
                    'NameNumber' => (string) $passangerIndex+1 .".1",
                ],
                "SegmentNumber" => "A"
            ];

            $secureFlights[] = [
                "PersonName" => [
                    'Gender' => ($passangerTitles[$passangerIndex] == 'Mr.' || $passangerTitles[$passangerIndex] == 'Mstr.') ? "M" : "F",
                    'GivenName' => str_replace(".","",$passangerTitles[$passangerIndex])." ".$firstName,
                    'Surname' => $lastNames[$passangerIndex],
                    'DateOfBirth' => (string) $dob[$passangerIndex],
                    'NameNumber' => (string) $passangerIndex+1 .".1",
                ],
                "SegmentNumber" => "A",
                "VendorPrefs" => [
                    "Airline" => [
                        'Hosted' => false
                    ]
                ]
            ];

            $found = false;
            foreach($pricingQualifiersPassengerTypes as $pricingQualifiersPassengerIndex => $pricingQualifiersPassengerType){
                if($pricingQualifiersPassengerType['Code'] == $passengerTypeForPersonName){
                    $pricingQualifiersPassengerTypes[$pricingQualifiersPassengerIndex] = [
                        "Code" => $passengerTypeForPersonName,
                        "Quantity" => (string) 2
                    ];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $pricingQualifiersPassengerTypes[] = [
                    "Code" => $passengerTypeForPersonName,
                    "Quantity" => (string) 1
                ];
            }

        }

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v2.5.0/passenger/records?mode=create';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v2.5.0/passenger/records?mode=create';
        }

        $request_body = array(
            "CreatePassengerNameRecordRQ" => array(
                "version" => "2.5.0",
                "targetCity" => (string) $sabreGdsInfo->pcc,
                "haltOnAirPriceError" => true,
                "TravelItineraryAddInfo" => array(
                    "AgencyInfo" => array(
                        "Address" => array(
                            "AddressLine" => "Faith Travels & Tours Ltd",
                            "CityName" => "Dhaka",
                            "CountryCode" => "BD",
                            "PostalCode" => "1213",
                            "StateCountyProv" => array(
                                "StateCode" => "BD"
                            ),
                            "StreetNmbr" => "DHAKA"
                        ),
                        "Ticketing" => array(
                            "TicketType" => "7TAW"
                        )
                    ),
                    "CustomerInfo" => array(
                        "ContactNumbers" => array(
                            "ContactNumber" => array(
                                array(
                                    "LocationCode" => "DAC",
                                    "NameNumber" => "1.1",
                                    "PhoneUseType" => "M",
                                    "Phone" => $travellerContact
                                )
                            )
                        ),
                        "Email" => array(
                            array(
                                "Address" => $travellerEmail,
                                "Type" => "CC"
                            )
                        ),
                        "PersonName" => $personName,
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
                        array("Code" => "US"),
                        array("Code" => "UN"),
                        array("Code" => "HX"),
                        array("Code" => "WL")
                    ),
                    "OriginDestinationInformation" => array(
                        "FlightSegment" => $flightSegment
                    ),
                    "RedisplayReservation" => array(
                        "NumAttempts" => 3,
                        "WaitInterval" => 3000
                    )
                ),
                "AirPrice" => array(
                    array(
                        "PriceRequestInformation" => array(
                            "Retain" => true,
                            "OptionalQualifiers" => array(
                                "FOP_Qualifiers" => array(
                                    "BasicFOP" => array(
                                        "Type" => "CASH"
                                    )
                                ),
                                "PricingQualifiers" => array(
                                    "PassengerType" => $pricingQualifiersPassengerTypes
                                )
                            )
                        )
                    )
                ),
                "SpecialReqDetails" => array(
                    "SpecialService" => array(
                        "SpecialServiceInfo" => array(
                            "AdvancePassenger" => $advancePassengers,
                            "SecureFlight" => $secureFlights,
                            "Service" => $specialServices
                        )
                    ),
                    "AddRemark" => array(
                        "RemarkInfo" => array(
                            "Remark" => array(
                                array(
                                    "Type" => "General",
                                    "Text" => "Booking Created from FaithTrip Portal"
                                ),
                            )
                        )
                    )
                ),
                "PostProcessing" => array(
                    "EndTransaction" => array(
                        "Source" => array(
                            "ReceivedFrom" => "FaithTrip B2B Portal"
                        ),
                        "Email" => array(
                            "Ind" => true,
                        )
                    ),
                    "RedisplayReservation" => array("waitInterval" => 8000),
                )
            )
        );


        // Convert the request body array to JSON format
        $request_json = json_encode($request_body);

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
