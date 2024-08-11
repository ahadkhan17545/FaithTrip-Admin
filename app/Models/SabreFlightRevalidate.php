<?php

namespace App\Models;

use App\Http\Controllers\FlightSearchController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SabreGdsConfig;
use DateTime;

class SabreFlightRevalidate extends Model
{
    use HasFactory;

    public static function flightRevalidateOld($sessionIndex){

        // Define your dynamic data
        $adult = session('adult');
        $child = session('child');
        $infant = session('infant');
        $seatsRequested = $adult+$child+$infant;

        // making passanger info array start
        $passengerTypes = array();
        if ($adult > 0) {
            $passengerTypes[] = array("Code" => "ADT", "Quantity" => (int) $adult);
        }
        if ($child > 0) {
            $passengerTypes[] = array("Code" => "CNN", "Quantity" => (int) $child);
        }
        if ($infant > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (int) $infant);
        }
        $airTravelerAvail = [];
        $passengerTypeQuantity = [];
        foreach ($passengerTypes as $passengerType) {
            $passengerTypeQuantity[] = $passengerType;
        }
        $airTravelerAvail[] = array(
            "PassengerTypeQuantity" => $passengerTypeQuantity
        );
        // making passanger info array end


        // fetching segment of flights
        $searchResults = json_decode(session('search_results'), true);
        $segmentArray = [];
        $legsArray = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['legs'];
        foreach ($legsArray as $key => $leg) {
            $legRef = $leg['ref'] - 1;
            $legDescription = $searchResults['groupedItineraryResponse']['legDescs'][$legRef];
            $schedulesArray = $legDescription['schedules'];
            foreach ($schedulesArray as $schedule) {
                $scheduleRef = $schedule['ref'] - 1;
                $segmentArray[] = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
            }
        }


        // echo "<pre>";
        // print_r($segmentArray);
        // echo "</pre>";
        // exit();


        $OriginDestinationInformation = [];
        foreach ($segmentArray as $key2 => $segmentData) {

            if($key2 == 0) {

                $departureDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($segmentData['departure']['time'], 0, 8);
                $elapsedDate = date('Y-m-d', strtotime($departureDateTime . ' +'.$segmentData['elapsedTime'].' minutes'));
                $arrivalDateTime = $elapsedDate."T".substr($segmentData['arrival']['time'], 0, 8);

            } else {

                $time1 = substr($segmentData['departure']['time'],0,8);
                $time2 = substr($segmentArray[$key2-1]['arrival']['time'],0,8);
                $time1Obj = DateTime::createFromFormat('H:i:s', $time1);
                $time2Obj = DateTime::createFromFormat('H:i:s', $time2);
                $interval = $time1Obj->diff($time2Obj);
                $totalMinutes = ($interval->h * 60) + $interval->i;

                // checking its a return segment or not
                if(isset($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]) && $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureLocation'] == $segmentData['departure']['airport']){
                    $departureDateTime = date('Y-m-d', strtotime($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']))."T".substr($segmentData['departure']['time'], 0, 8);
                } else {
                    $departureDateTime = date('Y-m-d', strtotime($OriginDestinationInformation[$key2-1]['TPA_Extensions']['Flight'][0]['ArrivalDateTime'] . ' +'.$totalMinutes.' minutes'))."T".substr($segmentData['departure']['time'], 0, 8);
                }

                $elapsedDate = date('Y-m-d', strtotime($departureDateTime . ' +'.$segmentData['elapsedTime'].' minutes'));
                $arrivalDateTime = $elapsedDate."T".substr($segmentData['arrival']['time'], 0, 8);

            }

            $originLocation = $segmentData['departure']['airport'];
            $destinationLocation = $segmentData['arrival']['airport'];
            $flightNumber = $segmentData['carrier']['marketingFlightNumber'];
            $operatingAirline = $segmentData['carrier']['operating'];
            $marketingAirline = $segmentData['carrier']['marketing'];

            // booking code
            $lastIndexOfPriceInfo = count($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'])-1;
            if(isset($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'][$lastIndexOfPriceInfo]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$key2]['segment']['bookingCode'])){
                $bookingCode = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'][$lastIndexOfPriceInfo]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$key2]['segment']['bookingCode'];
            } else {
                $bookingCode = "L";
            }

            // Create segment array
            $segment = [
                "RPH" => (string) 1,
                "DepartureDateTime" => $departureDateTime,
                "OriginLocation" => [
                    "LocationCode" => $originLocation
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationLocation
                ],
                "TPA_Extensions" => [
                    "SegmentType" => [
                        "Code" => "O"
                    ],
                    "Flight" => [
                        [
                            "Number" => $flightNumber,
                            "DepartureDateTime" => $departureDateTime,
                            "ArrivalDateTime" => $arrivalDateTime,
                            "Type" => "A",
                            "ClassOfService" => (string) $bookingCode, // "K",
                            "OriginLocation" => [
                                "LocationCode" => $originLocation
                            ],
                            "DestinationLocation" => [
                                "LocationCode" => $destinationLocation
                            ],
                            "Airline" => [
                                "Operating" => $operatingAirline,
                                "Marketing" => $marketingAirline
                            ]
                        ]
                    ]
                ]
            ];

            // Add segment to OriginDestinationInformation
            $OriginDestinationInformation[] = $segment;
        }


        // check for access token start
        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){
            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");
            if($currentDate >= $tokenExpireDate){
                FlightSearchController::generateAccessToken();
            }
        } else {
            FlightSearchController::generateAccessToken();
        }
        // check for access token end


        // Construct the data array
        $data = [
            "OTA_AirLowFareSearchRQ" => [
                "Version" => "5",
                "TravelPreferences" => [
                    "TPA_Extensions" => [
                        "VerificationItinCallLogic" => [
                            "Value" => "L"
                        ]
                    ]
                ],
                "TravelerInfoSummary" => [
                    "SeatsRequested" => [$seatsRequested],
                    "AirTravelerAvail" => $airTravelerAvail
                ],
                "POS" => [
                    "Source" => [
                        [
                            "PseudoCityCode" => "S00L",
                            "RequestorID" => [
                                "Type" => "1",
                                "ID" => "1",
                                "CompanyName" => [
                                    "Code" => "TN"
                                ]
                            ]
                        ]
                    ]
                ],
                "OriginDestinationInformation" => $OriginDestinationInformation,
                "TPA_Extensions" => [
                    "IntelliSellTransaction" => [
                        "RequestType" => [
                            "Name" => "50ITINS"
                        ]
                    ]
                ]
            ]
        ];

        // return $data;
        // exit();

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v5/shop/flights/revalidate';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v5/shop/flights/revalidate';
        }

        $authorizationToken = 'Authorization: Bearer '.session('access_token');
        $jsonData = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiEndPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Conversation-ID: 2021.01.DevStudio',
                $authorizationToken
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
        // return $OriginDestinationInformation;

    }

    public static function flightRevalidate($sessionIndex){

        // Define your dynamic data
        $adult = session('adult');
        $child = session('child');
        $infant = session('infant');
        $seatsRequested = $adult+$child+$infant;

        // making passanger info array start
        $passengerTypes = array();
        if ($adult > 0) {
            $passengerTypes[] = array("Code" => "ADT", "Quantity" => (int) $adult, "TPA_Extensions" => ["VoluntaryChanges" => ["Match" => "Info"]]);
        }
        if ($child > 0) {
            $passengerTypes[] = array("Code" => "CNN", "Quantity" => (int) $child, "TPA_Extensions" => ["VoluntaryChanges" => ["Match" => "Info"]]);
        }
        if ($infant > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (int) $infant, "TPA_Extensions" => ["VoluntaryChanges" => ["Match" => "Info"]]);
        }
        $passengerTypeQuantity = [];
        foreach ($passengerTypes as $passengerType) {
            $passengerTypeQuantity[] = $passengerType;
        }
        $airTravelerAvail[] = array(
            "PassengerTypeQuantity" => $passengerTypeQuantity
        );
        // making passanger info array end


        // fetching segment of flights
        $searchResults = json_decode(session('search_results'), true);
        $segmentArray = [];
        $legsArray = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['legs'];
        foreach ($legsArray as $key => $leg) {
            $legRef = $leg['ref'] - 1;
            $legDescription = $searchResults['groupedItineraryResponse']['legDescs'][$legRef];
            $schedulesArray = $legDescription['schedules'];
            foreach ($schedulesArray as $schedule) {
                $scheduleRef = $schedule['ref'] - 1;
                $segmentArray[] = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
            }
        }

        // echo "<pre>";
        // print_r($segmentArray);
        // echo "</pre>";
        // exit();

        $flightInformation = [];
        $firstDepartureDate = "";
        $firstOriginLocation = "";
        $lastArrivalLocation = "";

        $departureDate = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'];
        foreach ($segmentArray as $key2 => $segmentData) {

            if($key2 == 0) {
                $firstDepartureDate = $departureDate."T".substr($segmentData['departure']['time'], 0, 8);
                $firstOriginLocation = $segmentData['departure']['airport'];
            }

            $departureDateTime = new DateTime($departureDate . ' ' . $segmentData['departure']['time']);
            $arrivalDateTime = new DateTime($departureDate . ' ' . $segmentData['arrival']['time']);

            // Adjust the arrival date if there's a date adjustment
            if (isset($segmentData['arrival']['dateAdjustment']) && $segmentData['arrival']['dateAdjustment'] > 0) {
                $arrivalDateTime->modify('+' . $segmentData['arrival']['dateAdjustment'] . ' day');
            }

            $originLocation = $segmentData['departure']['airport'];
            $destinationLocation = $segmentData['arrival']['airport'];
            $flightNumber = $segmentData['carrier']['marketingFlightNumber'];
            $operatingAirline = $segmentData['carrier']['operating'];
            $marketingAirline = $segmentData['carrier']['marketing'];
            $lastArrivalLocation = $destinationLocation;

            // booking code
            $lastIndexOfPriceInfo = count($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'])-1;
            if(isset($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'][$lastIndexOfPriceInfo]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$key2]['segment']['bookingCode'])){
                $bookingCode = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'][$lastIndexOfPriceInfo]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$key2]['segment']['bookingCode'];
            } else {
                $bookingCode = "L";
            }

            $flights = [
                "Airline" => [
                    "Marketing" => $marketingAirline,
                    "Operating" => $operatingAirline
                ],
                "Number" => $flightNumber,
                "ClassOfService" => (string) $bookingCode,
                "OriginLocation" => [
                    "LocationCode" => $originLocation
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationLocation
                ],
                "DepartureDateTime" => $departureDateTime->format('Y-m-d')."T".$departureDateTime->format('H:i:s'),
                "ArrivalDateTime" => $arrivalDateTime->format('Y-m-d')."T".$arrivalDateTime->format('H:i:s'),
                "Type" => "A",
            ];

            $flightInformation[] = $flights;

        }



        $requestBody = [
            "OTA_AirLowFareSearchRQ" => [
                "Version" => "5",
                "TravelPreferences" => [
                    "TPA_Extensions" => [
                        "VerificationItinCallLogic" => [
                            "Value" => "L",
                            "AlwaysCheckAvailability" => true
                        ]
                    ]
                ],
                "TravelerInfoSummary" => [
                    "SeatsRequested" => [$seatsRequested],
                    "AirTravelerAvail" => $airTravelerAvail
                ],
                "POS" => [
                    "Source" => [
                        [
                            "PseudoCityCode" => "S00L",
                            "RequestorID" => [
                                "Type" => "1",
                                "ID" => "1",
                                "CompanyName" => [
                                    "Code" => "TN"
                                ]
                            ]
                        ]
                    ]
                ],
                "OriginDestinationInformation" => [
                    [
                        "RPH" => "1",
                        "DepartureDateTime" => $firstDepartureDate,
                        "OriginLocation" => [
                            "LocationCode" => $firstOriginLocation
                        ],
                        "DestinationLocation" => [
                            "LocationCode" => $lastArrivalLocation
                        ],
                        "TPA_Extensions" => [
                            "Flight" => $flightInformation
                        ]
                    ]
                ],
                "TPA_Extensions" => [
                    "IntelliSellTransaction" => [
                        "RequestType" => [
                            "Name" => "REVALIDATE"
                        ],
                        "ServiceTag" => [
                            "Name" => "REVALIDATE"
                        ]
                    ]
                ]
            ]
        ];

        $jsonRequestBody = json_encode($requestBody);


        // check for access token start
        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){
            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");
            if($currentDate >= $tokenExpireDate){
                FlightSearchController::generateAccessToken();
            }
        } else {
            FlightSearchController::generateAccessToken();
        }
        // check for access token end

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v5/shop/flights/revalidate';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v5/shop/flights/revalidate';
        }

        $authorizationToken = 'Authorization: Bearer '.session('access_token');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiEndPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonRequestBody,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Conversation-ID: 2021.01.DevStudio',
                $authorizationToken
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        // return $response;
        return $flightInformation;

    }
}
