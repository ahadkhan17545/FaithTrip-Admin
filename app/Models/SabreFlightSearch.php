<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class SabreFlightSearch extends Model
{
    use HasFactory;

    public static function generateAccessToken(){

        // developer account of Fahim
        $authorizationHeader = base64_encode(base64_encode("V1:hxp6cy145bjv5hy9:DEVCENTER:EXT").':'.base64_encode("Hp8tT6iN"));
        $apiEndPoint = 'https://api.cert.platform.sabre.com/v2/auth/token';

        // Faithtrip account
        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $username = base64_encode($sabreGdsInfo->user_id);
            $password = base64_encode($sabreGdsInfo->password);
            $authorizationHeader = base64_encode($username.":".$password);
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v2/auth/token';
        } else{
            $username = base64_encode($sabreGdsInfo->production_user_id);
            $password = base64_encode($sabreGdsInfo->production_password);
            $authorizationHeader = base64_encode($username.":".$password);
            $apiEndPoint = 'https://api.platform.sabre.com/v2/auth/token';
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
        CURLOPT_POSTFIELDS =>'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic '.$authorizationHeader,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        session(['access_token' => $accessToken]);
        session(['expires_in' => $expiresIn]);
    }

    public static function getFlightSearchResults($originCityCode, $destinationCityCode, $departureDate, $returnDate, $adult, $child, $infant, $flightType, $airlinePrefs){

        if(session('access_token') && session('access_token') != '' && session('expires_in') != ''){

            $seconds = session('expires_in');
            $date = new DateTime();
            $date->setTimestamp(time() + $seconds);
            $tokenExpireDate = $date->format('Y-m-d');
            $currentDate = date("Y-m-d");

            if($currentDate >= $tokenExpireDate){
                SabreFlightSearch::generateAccessToken();
            }

        } else {
            SabreFlightSearch::generateAccessToken();
        }

        // travellers info
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

        // oneway or return flights
        $flightTypeData = array();
        if($flightType == 1){

            $flightTypeData[] = array_filter([
                "RPH" => "1",
                "DepartureDateTime" => "$departureDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

        } else {

            $flightTypeData[] = array_filter([
                "RPH" => "1",
                "DepartureDateTime" => "$departureDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

            $flightTypeData[] = array_filter([
                "RPH" => "2",
                "DepartureDateTime" => "$returnDate" . "T00:00:00",
                "OriginLocation" => [
                    "LocationCode" => $destinationCityCode
                ],
                "DestinationLocation" => [
                    "LocationCode" => $originCityCode
                ],
                "TPA_Extensions" => $airlinePrefs !== null ? ["IncludeVendorPref" => $airlinePrefs] : null
            ], function($value) {
                return $value !== null;
            });

        }

        // Sabre API request payload with dynamic query

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v5/offers/shop';
            $requestTypeName = "200ITINS";
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v5/offers/shop';
            $requestTypeName = "50ITINS";
        }

        $accessToken = session('access_token');
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
            CURLOPT_POSTFIELDS => json_encode(array(
                "OTA_AirLowFareSearchRQ" => array(
                    "Version" => "5",
                    "POS" => array(
                        "Source" => array(
                            array(
                                "PseudoCityCode" => "S00L",
                                "RequestorID" => array(
                                    "Type" => "1",
                                    "ID" => "1",
                                    "CompanyName" => array(
                                        "Code" => "TN"
                                    )
                                )
                            )
                        )
                    ),
                    "OriginDestinationInformation" => $flightTypeData,
                    "TravelPreferences" => array(
                        "TPA_Extensions" => array(
                            "DataSources" => array(
                                "NDC" => "Disable",
                                "ATPCO" => "Enable",
                                "LCC" => "Disable"
                            ),
                            "PreferNDCSourceOnTie" => array(
                                "Value" => true
                            )
                        )
                    ),
                    "TravelerInfoSummary" => array(
                        "AirTravelerAvail" => $airTravelerAvail
                    ),
                    "TPA_Extensions" => array(
                        "IntelliSellTransaction" => array(
                            "RequestType" => array(
                                "Name" => $requestTypeName
                            )
                        )
                    )
                )
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Conversation-ID: ",
                "Authorization: Bearer $accessToken",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
