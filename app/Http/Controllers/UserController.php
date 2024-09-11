<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function createB2bUser(){
        return view('user.create');
    }

    public function saveB2bUser(Request $request){

        $image = null;
        if ($request->hasFile('image')){
            $file = $request->file('image');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('userImages/');
            $file->move($file_location, $file_name);
            $image = "userImages/" . $file_name;
        }

        $userId = User::insertGetId([
            'image' => $image,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nid' => $request->nid,
            'comission' => $request->comission,
            'password' => Hash::make($request->password),
            'status' => 1,
            'user_type' => 2,
            'created_at' => Carbon::now(),
        ]);

        $logo = null;
        if ($request->hasFile('brand_logo')){
            $file = $request->file('brand_logo');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('companyLogo/');
            $file->move($file_location, $file_name);
            $logo = "companyLogo/" . $file_name;
        }

        CompanyProfile::insert([
            'logo' => $logo,
            'user_id' => $userId,
            'name' => $request->company_name,
            'address' => $request->company_address,
            'phone' => $request->company_phone,
            'tin' => $request->tin,
            'bin' => $request->bin,
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('New B2B User Account Created');
        return back();

    }

    public function viewB2bUser(Request $request){
        if ($request->ajax()) {

            $data = DB::table('users')
                    ->leftJoin('company_profiles', 'users.id', 'company_profiles.user_id')
                    ->select('users.*', 'company_profiles.name as company_name')
                    ->where('users.user_type', 2)
                    ->orderBy('users.id', 'desc')
                    ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:red'>Inactive</span>";
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Active</span>";
                    })
                    ->editColumn('comission', function($data) {
                        return $data->comission."%";
                    })
                    ->addColumn('bookings', function($data) {
                        return DB::table('flight_bookings')->where('booked_by', $data->id)->count();
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/b2b/user')."/".$data->id.'" class="btn-sm btn-warning rounded d-inline-block mb-1"><i class="fa fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('user.view');
    }

    public function deleteB2bUser($id){
        $user = User::where('id', $id)->first();
        $companyProfile = CompanyProfile::where('user_id', $user->id)->first();
        if($companyProfile && $companyProfile->logo){
            if(file_exists(public_path($companyProfile->logo))){
                unlink(public_path($companyProfile->logo));
            }
        }
        if($user->image && file_exists(public_path($user->image))){
            unlink(public_path($user->image));
        }
        $companyProfile->delete();
        $user->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }

    public function editB2bUser($id){
        $user = User::where('id', $id)->first();
        $companyProfile = CompanyProfile::where('user_id', $user->id)->first();
        return view('user.update', compact('user', 'companyProfile'));
    }

    public function updateB2bUser(Request $request){
        $user = User::where('id', $request->user_id)->first();
        $companyProfile = CompanyProfile::where('user_id', $user->id)->first();

        $image = $user->image;
        if ($request->hasFile('image')){

            if($user->image && file_exists(public_path($user->image))){
                unlink(public_path($user->image));
            }

            $file = $request->file('image');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('userImages/');
            $file->move($file_location, $file_name);
            $image = "userImages/" . $file_name;
        }

        if($request->password){
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->password),
            ]);
        }

        User::where('id', $user->id)->update([
            'image' => $image,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nid' => $request->nid,
            'comission' => $request->comission,
            'status' => $request->status,
            'updated_at' => Carbon::now(),
        ]);

        $logo = $companyProfile->logo;
        if ($request->hasFile('brand_logo')){

            if($companyProfile->logo && file_exists(public_path($companyProfile->logo))){
                unlink(public_path($companyProfile->logo));
            }

            $file = $request->file('brand_logo');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('companyLogo/');
            $file->move($file_location, $file_name);
            $logo = "companyLogo/" . $file_name;
        }

        CompanyProfile::where('id', $companyProfile->id)->update([
            'logo' => $logo,
            'name' => $request->company_name,
            'address' => $request->company_address,
            'phone' => $request->company_phone,
            'tin' => $request->tin,
            'bin' => $request->bin,
            'updated_at' => Carbon::now(),
        ]);

        Toastr::success('B2B User Account Info Updated');
        return back();
    }

    public function savedPassangers(Request $request){
        if ($request->ajax()) {

            if(Auth::user()->user_type == 1){
                $data = DB::table('saved_passangers')
                        ->orderBy('id', 'desc')
                        ->get();
            } else {
                $data = DB::table('saved_passangers')
                        ->orderBy('id', 'desc')
                        ->where('saved_by', Auth::user()->id)
                        ->get();
            }

            return Datatables::of($data)
                    ->editColumn('first_name', function($data) {
                        return $data->title." ".$data->first_name." ".$data->last_name;
                    })
                    ->editColumn('document_no', function($data) {
                        if($data->document_no){
                            if($data->document_type == 1){
                                return "Passport: ".$data->document_no;
                            } else{
                                return "NID: ".$data->document_no;
                            }
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        // $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Edit" class="btn-sm btn-warning rounded d-inline-block mb-1 editButton"><i class="fa fa-edit"></i></a>';
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('user.saved_passsangers');

    }

    public function deleteSavedPassanger($id){
        DB::table('saved_passangers')->where('id', $id)->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }


}
