<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserVerificationEmail;
use App\Models\EmailConfigure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    const AUTHORIZATION_TOKEN = 'OTA-786845756UYUDJEU';

    public function userRegistration(Request $request){

        if ($request->header('Authorization') == self::AUTHORIZATION_TOKEN) {

            // need to send Accept: application/json if use this validation
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => ['required', 'string', 'max:20', 'unique:users', 'regex:/^\+?[0-9]{7,15}$/'],
                'password' => 'required|string|min:8',
            ]);

            // check for already registered or not
            User::where('email', $request->email)->where('email_verified_at', null)->delete();
            $checkExistingEmail = User::where('email', $request->email)->where('email_verified_at', '!=', null)->first();
            if($checkExistingEmail){
                return response()->json([
                    'success'=> false,
                    'message'=> 'You are already registered! Try login',
                    'data' => null
                ]);
            }

            $randomCode = rand(100000, 999999);
            User::insert([
                'image' => null,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'verification_code' => $randomCode,
                'email_verified_at' => null,
                'user_type' => 3,
                'balance' => 0,
                'status' => 0,
                'search_status' => 0,
                'created_at' => Carbon::now(),
            ]);

            $emailConfig = EmailConfigure::where('id', 1)->first();
            if($emailConfig){
                try {

                    // Dynamically set mail config
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.transport' => 'smtp',
                        'mail.mailers.smtp.host' => $emailConfig->host,
                        'mail.mailers.smtp.port' => $emailConfig->port,
                        'mail.mailers.smtp.encryption' => $emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : null),
                        'mail.mailers.smtp.username' => $emailConfig->email,
                        'mail.mailers.smtp.password' => $emailConfig->password,
                        'mail.from.address' => $emailConfig->mail_from_email,
                        'mail.from.name' => $emailConfig->mail_from_name ?? env('APP_NAME'),
                    ]);

                    $mailData = array();
                    $mailData['code'] = $randomCode;
                    Mail::to(trim($request->email))->send(new UserVerificationEmail($mailData));

                    return response()->json([
                        'success' => true,
                        'message' => "Verification Email Sent",
                        'data' => null
                    ], 200);


                } catch(\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => "Something Went Wrong while Sending Email",
                        'data' => null
                    ], 200);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Something went wrong while sending mail",
                    'data' => null
                ], 200);
            }


        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function userVerification(Request $request){
        if ($request->header('Authorization') == self::AUTHORIZATION_TOKEN) {

            // need to send Accept: application/json if use this validation
            $request->validate([
                'email' => 'required|email',
                'verification_code' => 'required|digits:6',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user || $user->verification_code !== $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid email or verification code.',
                ], 401);
            }

            // Mark user as verified
            $user->email_verified_at = Carbon::now();
            $user->updated_at = Carbon::now();
            $user->verification_code = null;
            $user->status = 1;
            $user->search_status = 1;
            $user->save();

            // Create token
            $data['token'] = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Verification successful.',
                'data' => $data
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function userLogin(Request $request){

        if ($request->header('Authorization') == self::AUTHORIZATION_TOKEN) {

            // need to send Accept: application/json if use this validation
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $data['token'] = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => $data
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // $request->user()->tokens()->delete(); // Deletes all access tokens for the user
        return response()->json(['message' => 'Logged out']);
    }

    public function forgotPassword(Request $request){

        if ($request->header('Authorization') == self::AUTHORIZATION_TOKEN) {

            // need to send Accept: application/json if use this validation
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Generate reset code
            $resetCode = rand(100000, 999999);
            $user->verification_code = $resetCode;
            $user->save();

            // Use your SMTP config logic here
            $emailConfig = EmailConfigure::where('id', 1)->first();
            if ($emailConfig) {

                // Dynamically set mail config
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $emailConfig->host,
                    'mail.mailers.smtp.port' => $emailConfig->port,
                    'mail.mailers.smtp.encryption' => $emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : null),
                    'mail.mailers.smtp.username' => $emailConfig->email,
                    'mail.mailers.smtp.password' => $emailConfig->password,
                    'mail.from.address' => $emailConfig->mail_from_email,
                    'mail.from.name' => $emailConfig->mail_from_name ?? env('APP_NAME'),
                ]);

                Mail::raw("Your password reset code is: $resetCode", function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Reset Code');
                });

                return response()->json([
                    'success' => true,
                    'message' => "Reset code sent to your email",
                    'data' => null
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Something went wrong while sending mail",
                    'data' => null
                ], 200);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }

    }

    public function resetPassword(Request $request){

        if ($request->header('Authorization') == self::AUTHORIZATION_TOKEN) {

            $request->validate([
                'email' => 'required|email',
                'reset_code' => 'required|digits:6',
                'password' => 'required|string|min:8',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || $user->verification_code !== $request->reset_code) {
                return response()->json(['message' => 'Invalid reset code.'], 400);
            }

            $user->password = Hash::make($request->password);
            $user->verification_code = null;
            $user->save();

            $data['token'] = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Password Reset & Logged in successful.',
                'data' => $data
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }

    }

    public function updateProfile(Request $request){
        if ($request->header('Authorization-Header') == self::AUTHORIZATION_TOKEN) {

            $user = $request->user(); // assuming Sanctum is used

            // need to send Accept: application/json if use this validation
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
                 'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+?[0-9]{7,15}$/',
                    'unique:users,phone,' . $request->user()->id
                ],
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->nid = $request->nid;

            if ($request->hasFile('image')){

                if($user->image && file_exists(public_path($user->image))){
                    unlink(public_path($user->image));
                }

                $get_image = $request->file('image');
                $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
                $location = public_path('userImages/');
                // Image::make($get_image)->save($location . $image_name, 50);
                $get_image->move($location, $image_name);
                $user->image = "userImages/" . $image_name;
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }

    public function submitAccountDeleteRequest(Request $request){
        if ($request->header('Authorization-Header') == self::AUTHORIZATION_TOKEN) {

            $user = $request->user();
            $user->delete_request_submitted = 1;
            $user->delete_request_submitted_at = Carbon::now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Account delete request submitted successfully',
                'data' => null
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid",
                'data' => null
            ], 422);
        }
    }


}
