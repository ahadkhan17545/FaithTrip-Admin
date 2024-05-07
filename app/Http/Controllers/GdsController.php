<?php

namespace App\Http\Controllers;

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
            
            $data = DB::table('products')
                        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                        ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                        ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                        ->select('products.*', 'units.name as unit_name', 'categories.name as category_name', 'flags.name as flag_name')
                        ->orderBy('products.id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('image', function($data) {
                        if(!$data->image || !file_exists(public_path(''. $data->image)))
                            return '';
                        else
                            return $data->image;
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return '<span class="btn btn-sm btn-success d-inline-block">Active</span>';
                        } else {
                            return '<span class="btn btn-sm btn-danger d-inline-block">Inactive</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $link = env('APP_FRONTEND_URL')."/product/details/".$data->slug;
                        $btn = ' <a target="_blank" href="'.$link.'" class="mb-1 btn-sm btn-success rounded d-inline-block" title="For Frontend Product View"><i class="fa fa-eye"></i></a>';
                        $btn .= ' <a href="'.url('edit/product').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded d-inline-block"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'price', 'status'])
                    ->make(true);
        }
        return view('backend.product.view');
    }
}
