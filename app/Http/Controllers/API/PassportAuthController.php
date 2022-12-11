<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassportAuthController extends Controller
{
    public function registerUserExample(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
        ]);
        $user= User::create([
            'name' =>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);

        $access_token_example = $user->createToken('PassportExample@Section.io')->access_token;
        //return the access token we generated in the above step
        return response()->json(['token'=>$access_token_example],200);
    }

    /**
     * login user to our application
     */
    public function loginUserExample(Request $request){
        $login_credentials=[
            'email'=>$request->email,
            'password'=>$request->password,
        ];
        if(auth()->attempt($login_credentials)){
            //generate the token for the user
            $user_login_token= auth()->user()->createToken(Auth::user()->email)->accessToken;
            //now return this token on success login attempt
            return response()->json(['name'=>Auth::user()->name,'email'=>Auth::user()->email,'token' => $user_login_token], 200);
        }
        else{
            //wrong login credentials, return, user not authorised to our system, return error code 401
            if($request->email == null || empty($request->email)){
                return response()->json(['error'=>config('custom.code.empty_field'), 'message' => config('custom.message.email_required')]);
            }
            if($request->password == null || empty($request->password)){
                return response()->json(['error'=>config('custom.code.empty_field'), 'message' => config('custom.message.password_required')]);
            }
            return response()->json(['error'=>config('custom.code.unauthorized'), 'message' => config('custom.message.login_invalid')]);
        }
    }

    /**
     * This method returns authenticated user details
     */
    public function authenticatedUserDetails(){
        //returns details
        return response()->json(['authenticated-user' => auth()->user()], 200);
    }
}
