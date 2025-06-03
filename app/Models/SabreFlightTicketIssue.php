<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SabreGdsConfig;

class SabreFlightTicketIssue extends Model
{
    use HasFactory;

    public static function issueTicket($pnrId){

        $itineraryId = $pnrId;

        $sabreGdsInfo = SabreGdsConfig::where('id', 1)->first();
        if($sabreGdsInfo->is_production == 0){
            $apiEndPoint = 'https://api.cert.platform.sabre.com/v1.3.0/air/ticket';
        } else{
            $apiEndPoint = 'https://api.platform.sabre.com/v1.3.0/air/ticket';
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
            CURLOPT_POSTFIELDS => json_encode(array(
                "AirTicketRQ" => array(
                "version" => "1.3.0",
                "targetCity" => "S00L",
                "DesignatePrinter" => array(
                    "Printers" => array(
                        "Ticket" => array(
                            "CountryCode" => "BD"
                        ),
                        "Hardcopy" => array(
                            "LNIATA" => "FF4A54"
                        ),
                        "InvoiceItinerary" => array(
                            "LNIATA" => "FF4A54"
                        )
                    )
                ),
                "Itinerary" => array(
                    "ID" => $itineraryId
                ),
                "Ticketing" => array(
                    array(
                    "MiscQualifiers" => array(
                        "Commission" => array(
                            "Percent" => 7
                        )
                    ),
                    "FOP_Qualifiers" => array(
                        "BasicFOP" => array(
                            "Type" => "CA"
                        )
                    ),
                    "PricingQualifiers" => array(
                        "PriceQuote" => array(
                            array(
                                "Record" => array(
                                    array(
                                        "Number" => 1,
                                        "Reissue" => false
                                    )
                                )
                            )
                            )
                        )
                    )
                ),
                "PostProcessing" => array(
                    "EndTransaction" => array(
                        "Source" => array(
                            "ReceivedFrom" => "FaithTrip"
                        )
                    )
                )
                )
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Conversation-ID: 2021.01.DevStudio',
                'Authorization: Bearer  '. session('access_token'),
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
