<?php

namespace App\Models;

use App\Http\Controllers\FlightSearchController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class SabreFlightRevalidate extends Model
{
    use HasFactory;

    public static function flightRevalidate($sessionIndex){

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


        $OriginDestinationInformation = [];
        foreach ($segmentArray as $key2 => $segmentData) {

            // modify departure date if the date change
            if($key2 == 0) {
                $departureDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($segmentData['departure']['time'], 0, 8);
            } else{
                $departureDateTime = substr($OriginDestinationInformation[$key2-1]['TPA_Extensions']['Flight'][0]['ArrivalDateTime'],0,10)."T".substr($segmentData['departure']['time'], 0, 8);
            }

            // modify arrival date if the date change
            $originalDepartureDateTime = new DateTime($departureDateTime);
            $dateTime = new DateTime($departureDateTime);
            $dateTime->modify("+" . $segmentData['elapsedTime'] . " minutes");
            $originalDate = $originalDepartureDateTime->format('Y-m-d');
            $modifiedDate = $dateTime->format('Y-m-d');
            if ($originalDate != $modifiedDate) {
                $newdateTime = DateTime::createFromFormat("Y-m-d\TH:i:s", $departureDateTime);
                $newdateTime->modify("+1 day");
                $arrivalDateTime = $dateTime->format("Y-m-d\TH:i:s");
            } else {
                $arrivalDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($segmentData['arrival']['time'], 0, 8);
            }


            $originLocation = $segmentData['departure']['city'];
            $destinationLocation = $segmentData['arrival']['city'];
            $flightNumber = $segmentData['carrier']['operatingFlightNumber'];
            $operatingAirline = $segmentData['carrier']['operating'];
            $marketingAirline = $segmentData['carrier']['marketing'];


            // Create segment array
            $segment = [
                "RPH" => (string) ($key2 + 1),
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
                            "ClassOfService" => "K",
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
                            "Value" => "B"
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

        $authorizationToken = 'Authorization: Bearer '.session('access_token');
        $jsonData = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.cert.platform.sabre.com/v5/shop/flights/revalidate',
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

    }
}
