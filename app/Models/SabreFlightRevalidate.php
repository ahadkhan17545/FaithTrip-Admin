<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SabreGdsConfig;
use App\Models\SabreFlightSearch;
use DateTime;

class SabreFlightRevalidate extends Model
{
    use HasFactory;

    public static function flightRevalidate($sessionIndex){

        // Fetch dynamic data from session
        $adult = session('adult', 0);
        $child = session('child', 0);
        $infant = session('infant', 0);
        $seatsRequested = $adult + $child + $infant;

        // Build passenger info array
        $passengerTypes = [];
        $passengerData = [
            'ADT' => $adult,
            'CNN' => $child,
            'INF' => $infant,
        ];
        foreach ($passengerData as $code => $quantity) {
            if ($quantity > 0) {
                $passengerTypes[] = [
                    "Code" => $code,
                    "Quantity" => (int)$quantity,
                    "TPA_Extensions" => ["VoluntaryChanges" => ["Match" => "Info"]],
                ];
            }
        }
        $airTravelerAvail = [["PassengerTypeQuantity" => $passengerTypes]];

        // Fetch segment of flights
        $searchResults = json_decode(session('search_results'), true);
        $legsArray = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['legs'];
        $segmentArray = [];

        foreach ($legsArray as $leg) {
            $legRef = $leg['ref'] - 1;
            $schedulesArray = $searchResults['groupedItineraryResponse']['legDescs'][$legRef]['schedules'];
            foreach ($schedulesArray as $schedule) {
                $scheduleRef = $schedule['ref'] - 1;
                $segment = $searchResults['groupedItineraryResponse']['scheduleDescs'][$scheduleRef];
                if (isset($schedule['departureDateAdjustment'])) {
                    $segment['bothDateAdjustment'] = $schedule['departureDateAdjustment'];
                }
                $segmentArray[] = $segment;
            }
        }

        // echo "<pre>";
        // print_r($segmentArray);
        // echo "</pre>";
        // exit();

        // Initialize variables for onward and return flights
        $onwardFlightInformation = [];
        $returnFlightInformation = [];
        $isReturnFlight = false;
        $firstDepartureDate = $firstOriginLocation = $lastArrivalLocation = "";
        $returnfirstDepartureDate = $returnfirstOriginLocation = $returnlastArrivalLocation = "";

        // Get departure date for onward and return flights
        $onwardDepartureDate = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][0]['departureDate'];
        $returnDepartureDate = isset($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate']) ? $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureDate'] : null;

        foreach ($segmentArray as $key2 => $segmentData) {

            // Check if this is the first segment
            if ($key2 == 0) {
                $firstDepartureDate = $onwardDepartureDate . "T" . substr($segmentData['departure']['time'], 0, 8);
                $firstOriginLocation = $segmentData['departure']['airport'];
            }

            // Check if this is a return flight segment
            if ($returnDepartureDate && $segmentData['departure']['airport'] == $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureLocation']) {
                $isReturnFlight = true;
            }

            // Determine the departure date for each segment
            $departureDate = $isReturnFlight ? $returnDepartureDate : $onwardDepartureDate;

            $departureDateTime = new DateTime($departureDate . ' ' . $segmentData['departure']['time']);
            $arrivalDateTime = new DateTime($departureDate . ' ' . $segmentData['arrival']['time']);

            if (isset($segmentData['bothDateAdjustment']) && $segmentData['bothDateAdjustment'] >= 1) {
                $departureDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');
                $arrivalDateTime->modify('+' . $segmentData['bothDateAdjustment'] . ' day');

                if (isset($segmentData['arrival']['dateAdjustment']) && $segmentData['arrival']['dateAdjustment'] > 0) {
                    $arrivalDateTime->modify('+' . $segmentData['arrival']['dateAdjustment'] . ' day');
                }

            } else {
                if (isset($segmentData['arrival']['dateAdjustment']) && $segmentData['arrival']['dateAdjustment'] > 0) {
                    $arrivalDateTime->modify('+' . $segmentData['arrival']['dateAdjustment'] . ' day');
                }
            }

            // Check if this is a return flight segment
            if ($returnDepartureDate && $segmentData['departure']['airport'] == $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'][1]['departureLocation']) {
                $returnfirstDepartureDate = $returnDepartureDate . "T" . substr($segmentData['departure']['time'], 0, 8);
                $returnfirstOriginLocation = $segmentData['departure']['airport'];
            }

            $originLocation = $segmentData['departure']['airport'];
            $destinationLocation = $segmentData['arrival']['airport'];
            $flightNumber = $segmentData['carrier']['marketingFlightNumber'];
            $operatingAirline = $segmentData['carrier']['operating'];
            $marketingAirline = $segmentData['carrier']['marketing'];

            // Booking code
            $lastIndexOfPriceInfo = array_key_last($searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation']);
            $bookingCode = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['itineraries'][$sessionIndex]['pricingInformation'][$lastIndexOfPriceInfo]['fare']['passengerInfoList'][0]['passengerInfo']['fareComponents'][0]['segments'][$key2]['segment']['bookingCode'] ?? "L";

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
                "DepartureDateTime" => $departureDateTime->format('Y-m-d') . "T" . $departureDateTime->format('H:i:s'),
                "ArrivalDateTime" => $arrivalDateTime->format('Y-m-d') . "T" . $arrivalDateTime->format('H:i:s'),
                "Type" => "A",
            ];

            if (!$isReturnFlight) {
                $lastArrivalLocation = $destinationLocation;
                $onwardFlightInformation[] = $flights;
            } else {
                $returnlastArrivalLocation = $destinationLocation;
                $returnFlightInformation[] = $flights;
            }
        }

        // Origin-Destination Information Array
        $originDestinationInformationArray = [];
        $legDescriptions = $searchResults['groupedItineraryResponse']['itineraryGroups'][0]['groupDescription']['legDescriptions'];

        if (count($legDescriptions) == 1) {
            $originDestinationInformationArray[0] = [
                "RPH" => "1",
                "DepartureDateTime" => $firstDepartureDate,
                "OriginLocation" => [
                    "LocationCode" => $firstOriginLocation
                ],
                "DestinationLocation" => [
                    "LocationCode" => $lastArrivalLocation
                ],
                "TPA_Extensions" => [
                    "Flight" => $onwardFlightInformation
                ]
            ];
        } else {
            $originDestinationInformationArray[0] = [
                "RPH" => "1",
                "DepartureDateTime" => $firstDepartureDate,
                "OriginLocation" => [
                    "LocationCode" => $firstOriginLocation
                ],
                "DestinationLocation" => [
                    "LocationCode" => $lastArrivalLocation
                ],
                "TPA_Extensions" => [
                    "Flight" => $onwardFlightInformation
                ]
            ];
            $originDestinationInformationArray[1] = [
                "RPH" => "2",
                "DepartureDateTime" => $returnfirstDepartureDate,
                "OriginLocation" => [
                    "LocationCode" => $returnfirstOriginLocation
                ],
                "DestinationLocation" => [
                    "LocationCode" => $returnlastArrivalLocation
                ],
                "TPA_Extensions" => [
                    "Flight" => $returnFlightInformation
                ]
            ];
        }

        // Build request body
        $requestBody = [
            "OTA_AirLowFareSearchRQ" => [
                "Version" => "6.8.0",
                "TravelPreferences" => [
                    "TPA_Extensions" => [
                        "VerificationItinCallLogic" => [
                            "Value" => "L",
                            "AlwaysCheckAvailability" => true
                        ],
                    ],
                    "Baggage" => [
                        "RequestType" => "A",
                        "Description" => true
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
                                    "Code" => "TN",
                                    "content" => "TN"
                                ]
                            ]
                        ]
                    ]
                ],
                "OriginDestinationInformation" => $originDestinationInformationArray,
                "TPA_Extensions" => [
                    "IntelliSellTransaction" => [
                        "RequestType" => ["Name" => "REVALIDATE"],
                        "ServiceTag" => ["Name" => "REVALIDATE"]
                    ]
                ]
            ]
        ];

        $jsonRequestBody = json_encode($requestBody);

        // Check for access token and refresh if necessary
        $expiresIn = session('expires_in', 0);
        if (session('access_token') && $expiresIn) {
            $tokenExpireDate = (new DateTime())->setTimestamp(time() + $expiresIn)->format('Y-m-d');
            if (date("Y-m-d") >= $tokenExpireDate) {
                SabreFlightSearch::generateAccessToken();
            }
        } else {
            SabreFlightSearch::generateAccessToken();
        }

        // Determine API endpoint
        $sabreGdsInfo = SabreGdsConfig::find(1);
        $apiEndPoint = $sabreGdsInfo->is_production ? 'https://api.platform.sabre.com/v5/shop/flights/revalidate' : 'https://api.cert.platform.sabre.com/v5/shop/flights/revalidate';

        // Execute cURL request
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
                'Authorization: Bearer ' . session('access_token'),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
        // return $originDestinationInformationArray;

    }
}
