<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
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
        return redirect()->back()->withErrors(['success_message' => 'Company Profile Updated']);
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

            return redirect()->back()->withErrors(['success_message' => 'Company Profile Updated']);

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

            return redirect()->back()->withErrors(['success_message' => 'Company Profile Updated']);

        }
    }

    public function myProfile(){
        return view('profile.user');
    }

    public function updateProfile(Request $request){

        $userProfile = User::where('id', Auth::user()->id)->first();

        $image = $userProfile->image;
        if ($request->hasFile('image')){
            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('userImages/');
            $get_image->move($location, $image_name);
            $image = "userImages/" . $image_name;
        }

        User::where('id', Auth::user()->id)->update([
            'image' => $image,
            'name' => $request->name,
            'phone' => $request->phone,
            'updated_at' => Carbon::now(),
        ]);

        if($request->curent_password && $request->new_password){
            if (Hash::check($request->curent_password, Auth::user()->password)){
                
                User::where('id', Auth::user()->id)->update([
                    'password' => Hash::make($request->new_password),
                ]);

            } else {
                return redirect()->back()->withErrors(['error_message' => 'Wrong Current Password']);
            }

        }

        Toastr::success('User Profile Updated', 'Success');
        return back();

    }

    public function removeUserImage(){
        $userProfile = User::where('id', Auth::user()->id)->first();
        if (file_exists(public_path($userProfile->image))){
            unlink(public_path($userProfile->image));
            $userProfile->image = null;
            $userProfile->updated_at = Carbon::now();
            $userProfile->save();
        }

        Toastr::success('User Profile Updated', 'Success');
        return back();

    }

}
