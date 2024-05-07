<?php

namespace App\Http\Controllers;

use App\Models\ExcludedAirlines;
use App\Models\Gds;
use App\Models\SabreGdsConfig;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
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

    public function viewExcludedAirlines(Request $request){

        if ($request->ajax()) {
            $data = ExcludedAirlines::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Edit" class="btn-sm btn-warning rounded d-inline-block editBtn"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('excluded_airlines');

    }

    public function saveExcludedAirline(Request $request){

        if($request->airline_id > 0){
            ExcludedAirlines::where('id', $request->airline_id)->update([
                'name' => $request->name,
                'code' => $request->code,
                'updated_at' => Carbon::now()
            ]);
        } else {
            ExcludedAirlines::insert([
                'name' => $request->name,
                'code' => $request->code,
                'created_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => 'Saved Successfully.']);
    }

    public function deleteExcludedAirline($id){
        ExcludedAirlines::where('id', $id)->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }

    public function excludedAirlineInfo($id){
        $data = ExcludedAirlines::where('id', $id)->first();
        return response()->json($data);
    }
}
