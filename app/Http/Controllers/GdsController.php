<?php

namespace App\Http\Controllers;

use App\Models\Gds;
use App\Models\SabreGdsConfig;
use Carbon\Carbon;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GdsController extends Controller
{
    public function setupGds(){
        $gds = Gds::orderBy('serial', 'asc')->get();
        return view('setup_gds', compact('gds'));
    }

    public function gdsStatusUpdate(Request $request){
        Gds::where('code', $request->gds_code)->update([
            'status' => $request->gds_status,
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['success' => 'Updated Successfully.']);
    }

    public function editGdsInfo($code){

        if($code == 'sabre'){
            $sabreGdsInfo = DB::table('sabre_gds_configs')
                            ->select('sabre_gds_configs.*', 'gds.name', 'gds.code', 'gds.logo')
                            ->leftJoin('gds', 'sabre_gds_configs.gds_id', '=', 'gds.id')
                            ->where('sabre_gds_configs.id', 1)
                            ->first();
            return view('gds.sabre', compact('sabreGdsInfo'));
        }

    }

    public function updateSabreGdsInfo(Request $request){
        SabreGdsConfig::where('id', 1)->update([
            'user_id' => $request->user_id,
            'password' => $request->password,
            'description' => $request->description,
            'updated_at' => Carbon::now()
        ]);
        return redirect()->back()->withErrors(['success_message' => 'Company Profile Updated']);
    }
}
