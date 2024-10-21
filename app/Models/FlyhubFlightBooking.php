<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FlyhubFlightBooking extends Model
{
    use HasFactory;

    public static function updateTravellers($request, $revalidatedData){

        $booking_tracking_id = $revalidatedData['flyhub_booking_tracking_id'];
        $member_id = "1";
        $save_pax = "no";

        $passenger = [];
        foreach($request->first_name as $passangerIndex => $firstName){

            $type = $request->passanger_type[$passangerIndex];
            $title = $request->titles[$passangerIndex];
            $docIssueCountry = DB::table('country')->where('iso3', $request->document_issue_country[$passangerIndex])->first();

            $passenger[] = array(
                "pax_id" => (string) $passangerIndex+1,
                "pax_type" => (string) $type == "ADT" ? "ADT" : ($type == "CHD" ? "CNN" : "INF"), // ADT = Adult , CNN = Child , INF = Infant
                "gender" => (string) ($title == "Mr." || $title == "Mstr.") ? "M" : "F",
                "title" => (string) str_replace('.', '', $title), // Mr/Mrs/Ms/Miss/Mstr
                "first_name" => (string) $firstName,
                "last_name" => (string) $request->last_name[$passangerIndex],
                "dob" => (string) $request->dob[$passangerIndex],
                "doc_country" => (string) $docIssueCountry->iso,
                "doc_no" => (string) $request->document_no[$passangerIndex],
                "doc_dateofexpiry" => (string) $request->document_expire_date[$passangerIndex],
                "doc_dateofissue" => (string) "",
                "frequent_flyer" => (string) $request->frequent_flyer_no[$passangerIndex],
                "isd_code" => (string) "880",
                "contact_no" => (string) substr($request->phone[$passangerIndex], -11),
                "email_address" => (string) $request->email[$passangerIndex],
                "wheelchair_required" => (string) "no"
            );
        }

        // Prepare data as an associative array
        $postData = [
            "booking_tracking_id" => $booking_tracking_id,
            "member_id" => $member_id,
            "save_pax" => $save_pax,
            "passenger" => $passenger
        ];

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $flyhubGds->api_endpoint.'/flight/update-travellers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'apikey: '.$flyhubGds->api_key,
                'secretecode: '.$flyhubGds->secret_code,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    public static function createBooking($request, $revalidatedData){

        $postData = array(
            "booking_tracking_id" => $revalidatedData['flyhub_booking_tracking_id'],
            "member_id" => 1,
            "isd_code" => "880",
            "contact_no" => (string) substr($request->traveller_contact, -10),
            "email_address" => (string) $request->traveller_email,
            "payment_type" => "account_balance",
            "redirect_url" => (string) ""
        );

        // Getting credentials from GDS Config
        $flyhubGds = FlyhubGdsConfig::where('id', 1)->first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $flyhubGds->api_endpoint.'/flight/create-booking',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'apikey: '.$flyhubGds->api_key,
                'secretecode: '.$flyhubGds->secret_code,
            ),
        ));

        // Execute the request
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }
}
