<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsGateway;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class SystemController extends Controller
{
    public function viewSmsGateways(){
        $gateways = SmsGateway::orderBy('id', 'asc')->get();
        return view('system.sms_gateway', compact('gateways'));
    }

    public function updateSmsGatewayInfo(Request $request){
        $provider = $request->provider;

        DB::table('sms_gateways')->update([
            'status' => 0,
            'updated_at' => Carbon::now()
        ]);

        if($provider == 'elitbuzz'){ //ID 1 => Elitbuzz
            SmsGateway::where('id', 1)->update([
                'api_endpoint' => $request->api_endpoint,
                'api_key' => $request->api_key,
                'sender_id' => $request->sender_id,
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if($provider == 'revesms'){ //ID 2 => Revesms
            SmsGateway::where('id', 2)->update([
                'api_endpoint' => $request->api_endpoint,
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'sender_id' => $request->sender_id,
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if($provider == 'khudebarta'){ //ID 2 => Revesms
            SmsGateway::where('id', 3)->update([
                'api_endpoint' => $request->api_endpoint,
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'sender_id' => $request->sender_id,
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        Toastr::success('Info Updated', 'Success');
        return back();

    }

    public function changeGatewayStatus($provider){

        DB::table('sms_gateways')->update([
            'status' => 0,
            'updated_at' => Carbon::now()
        ]);

        if($provider == 'elitbuzz'){ //ID 1 => Elitbuzz
            SmsGateway::where('id', 1)->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if($provider == 'revesms'){ //ID 2 => Revesms
            SmsGateway::where('id', 2)->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if($provider == 'khudebarta'){ //ID 2 => Revesms
            SmsGateway::where('id', 3)->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => 'Updated Successfully.']);

    }
}
