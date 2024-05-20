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
            $passengerTypes[] = array("Code" => "CHD", "Quantity" => (int) $child);
        }
        if ($infant > 0) {
            $passengerTypes[] = array("Code" => "INF", "Quantity" => (int) $infant);
        }
        $airTravelerAvail = [];
        foreach ($passengerTypes as $passengerType) {
            $airTravelerAvail[] = array(
                "PassengerTypeQuantity" => array($passengerType)
            );
        }
        // making passanger info array end

        $searchResults = json_decode(session('search_results'), true);
        $originLocation = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureLocation'];
        $destinationLocation = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['arrivalLocation'];

        // getting flight data from selected flight of search result start
        $legRef = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['legs'][0]['ref'];
        $scheduleRef = $searchResults['groupedItineraryResponse']['legDescs'][$legRef-1]['schedules'][0]['ref'];
        $scheduleDescription = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef-1];
        // getting flight data from selected flight of search result end

        $flightNumber = $scheduleDescription['carrier']['operatingFlightNumber'];
        $departureDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($scheduleDescription['departure']['time'], 0, 8);
        $arrivalDateTime = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate']."T".substr($scheduleDescription['arrival']['time'], 0, 8);
        $operatingAirline = $scheduleDescription['carrier']['operating'];
        $marketingAirline = $scheduleDescription['carrier']['marketing'];


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
                "OriginDestinationInformation" => [
                    [
                        "RPH" => "1",
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
                    ]
                ],
                "TPA_Extensions" => [
                    "IntelliSellTransaction" => [
                        "RequestType" => [
                            "Name" => "50ITINS"
                        ]
                    ]
                ]
            ]
        ];

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
