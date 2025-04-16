<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class logincontroller extends Controller
{
    public function submit(Request $request)
    {
        //Validate the phone number
        $request->validate([
            'phone'=>'required|numeric|min:10'
        ]);
        //find or create the user
        $user = User::firstOrCreate([
            'phone'=>$request->phone
        ]);

        if(!$user){
            return response()->json([
                'message'=>'User not found with that phone number'
            ],401);
        }
        //send otp
        $user->notify(new LoginNeedsVerification());
        //return the response
        return response()->json([
            'message'=>'Login code sent successfully'
        ],200);
    }

    public function verify(Request $request)
    {
        //validate the incoming request
        $request->validate([
            'phone'=>'required|numeric|min:10',
            'logincode'=>'required|numeric|between:111111,999999'
        ]);
        //find the user
        $user = User::where('phone',$request->phone)
        ->where('logincode',$request->logincode)
        ->first();
        //is the code provided the same one saved?
        //if yes return back an auth token
        if($user){
            $user->update([
                'logincode'=>null
            ]);
            return $user->createToken($request->logincode)->plainTextToken;
        }
        //if no return back a message
        return response()->json([
            'message'=>'Invalid login code'
        ],401);
    }
}
