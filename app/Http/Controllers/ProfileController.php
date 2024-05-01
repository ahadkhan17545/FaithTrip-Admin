<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function companyProfile(){
        $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
        return view('profile.company', compact('companyProfile'));
    }

    public function removeCompanyLogo(){
        $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
        if ($companyProfile && file_exists(public_path($companyProfile->logo))){
            unlink(public_path($companyProfile->logo));
            $companyProfile->logo = null;
            $companyProfile->updated_at = Carbon::now();
            $companyProfile->save();
        }
        Toastr::success('Company Brand Logo Removed', 'Success');
        return back();
    }

    public function updateCompanyProfile(Request $request){

        $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
        if($companyProfile){

            $image = $companyProfile->logo;
            if ($request->hasFile('logo')){
                $get_image = $request->file('logo');
                $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
                $location = public_path('companyLogo/');
                $get_image->move($location, $image_name);
                $image = "companyLogo/" . $image_name;
            }

            CompanyProfile::where('user_id', Auth::user()->id)->update([
                'logo' => $image,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'tin' => $request->tin,
                'bin' => $request->bin,
                'updated_at' => Carbon::now(),
            ]);

            $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
            $successMsg = "Company Profile Updated";
            return view('profile.company', compact('companyProfile', 'successMsg'));

        } else {

            $image = null;
            if ($request->hasFile('logo')){
                $get_image = $request->file('logo');
                $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
                $location = public_path('companyLogo/');
                $get_image->move($location, $image_name);
                $image = "companyLogo/" . $image_name;
            }

            CompanyProfile::insert([
                'user_id' => Auth::user()->id,
                'logo' => $image,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'tin' => $request->tin,
                'bin' => $request->bin,
                'created_at' => Carbon::now(),
            ]);

            $companyProfile = CompanyProfile::where('user_id', Auth::user()->id)->first();
            $successMsg = "Company Profile Updated";
            return view('profile.company', compact('companyProfile', 'successMsg'));

        }
    }

    public function myProfile(){
        return view('profile.user');
    }
}
