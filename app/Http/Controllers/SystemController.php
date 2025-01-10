<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Models\SmsGateway;
use App\Models\EmailConfigure;
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

    public function viewEmailConfig(){
        $config = EmailConfigure::where('id', 1)->first();
        return view('system.email_config', compact('config'));
    }

    public function updateEmailConfig(Request $request){
        EmailConfigure::where('id', 1)->update([
            'host' => $request->host,
            'port' => $request->port,
            'email' => $request->email,
            'password' => $request->password,
            'mail_from_name' => $request->mail_from_name,
            'mail_from_email' => $request->mail_from_email,
            'encryption' => $request->encryption,
            'created_at' => Carbon::now()
        ]);

        return redirect()->back()->withErrors(['success_message' => 'Email Config Updated']);
    }

    public function searchResultsViewConfig(){
        $config = Config::where('id', 1)->first();
        return view('system.search_results_view', compact('config'));
    }

    public function changeSearchResultsView($value){
        Config::where('id', 1)->update([
            'search_results_view' => $value,
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['success' => 'Updated Successfully.']);
    }

}
