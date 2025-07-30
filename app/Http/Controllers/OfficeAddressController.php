<?php

namespace App\Http\Controllers;

use App\Models\OfficeAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class OfficeAddressController extends Controller
{
    public function viewOfficeAddress(Request $request){
        if ($request->ajax()) {

            $query = OfficeAddress::orderBy('id', 'desc');

            return Datatables::of($query)
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:red'>Inactive</span>";
                        else
                            return "<span style='font-weight:600; color:green'>Active</span>";
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Edit" class="btn-sm btn-warning rounded d-inline-block editBtn"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'office_address'])
                    ->make(true);
        }
        return view('office_address');
    }

    public function saveOfficeAddress(Request $request){
        OfficeAddress::insert([
            'office_name' => $request->office_name,
            'office_address' => $request->office_address,
            'slug' => str::random(3) . "-" . time(),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);
        return response()->json(['success' => 'Added successfully.']);
    }

    public function deleteOfficeAddress($slug){
        OfficeAddress::where('slug', $slug)->delete();
        return response()->json(['success' => 'Deleted successfully.']);
    }

    public function getOfficeAddress($slug){
        $data = OfficeAddress::where('slug', $slug)->first();
        return response()->json($data);
    }

    public function updateOfficeAddress(Request $request){
        OfficeAddress::where('id', $request->office_address_id)->update([
            'office_name' => $request->office_name_update,
            'office_address' => $request->office_address_update,
            'status' => $request->office_address_status,
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['success'=>'Updated successfully.']);
    }

}
