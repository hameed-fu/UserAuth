<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\UserMail;
use Illuminate\Support\Str;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\PasswordResetMail;
use App\Http\Requests\Users\Login;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CheckID;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Users\Register;
use App\Http\Requests\Users\ChangePassword;
use App\Http\Requests\Users\ForgotPassword;

class UserController extends Controller
{
    public function sendInvitation(Request $request){
        
       \Mail::to($request->input('email'))->send(new InvitationMail());
       return response()->json(['message'=>'Invitation has been sent'],200);
    }

    public function register(Request $request){
        $user = User::create([
           'name'               => $request->input('name'),
           'user_name'          => $request->input('user_name'),
           'role'               => "User",
           'verification_code'  => \Str::random(6),
           'email'              => $request->input('email'),
           'password'           => \Hash::make($request->input('password')),
       ]);       
       \Mail::to($user->email)->send(new UserMail($user));
       return $user ? response()->json(['message'=>'You have been registered successfully ! An activation code has been sent to '.$user->email,'data'=> $user ],200): response()->json(['message'=>'Failed to create your account !'],422);
    }

    public function login(Login $request)
    {
        $user = User::where(['email'=> $request->email])->first();
        if ($user) 
        {
            if ($user['status'] == true) {
                if (!\Hash::check(request()->password, $user->password)) {
                    return response()->json(['message'=> 'Incorrect email or passwrod'],400);
                }
                \Auth::login($user);
                $token = $user->createToken('access_token')->accessToken;
                $user['access_token'] = $token;
                return $user ? response()->json(['message'=>'Logged in successfully','data'=> $user],200): response()->json(['message'=>'Failed to login'],400);
            }else{
                return response()->json(['Status' => false,'ErrorCode' => 400,'message'=> 'Your account is not active. please contact to admin for activation'],400);
            }
        }
        else {
            return response()->json(['message'=> 'User not found!'],404);
        }
    }
    public function verify(Request $request) {
        $user = User::where(['email'=> $request->email,'verification_code' => $request->verification_code])->first();
        
        if(!empty($user)  && $user->verification_code == $request->verification_code){
            $user->status = true;
            $user->email_verified_at = \Carbon\Carbon::now();
            $verified = $user->save();
           return $verified ? response()->json(['message'=>'Account Verified Successfully'],200): response()->json(['message'=>'Failed to verify user'],400);
        } else {
            return response()->json(['message'=>'Something went wrong!'],400);
        }
    }
   

}
