<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return $message;
        }

        $user = User::create([
            'email'    => $request->input('email'),
            'name'     => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'api_token'=> Str::random(60),
            'uuid'     => Str::uuid(),
        ]);

        return $user;
        
    }

    public function login(Request $request){
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return $message;
        }
        
        if(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])){
            $user = User::where('email', $request->input('email'))->firstOrFail();
            return $user;
        }else{
            return response()->json('nok');
        }
    }
}
