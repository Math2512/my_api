<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
        $getInfo = Socialite::driver($provider)->stateless()->user();
        
        $user = $this->createUser($getInfo, $provider);
        return redirect("http://my-app.test:3000/$user->api_token");
        return $user->api_token;
    }

    public function createUser($getInfo, $provider)
    {
        $user = User::where('provider_id', $getInfo->id)->first();
        if(!$user)
        {
            $user = User::create([
                'email'       => $getInfo->email,
                'name'        => $getInfo->name,
                'provider'    => $provider,
                'provider_id' => $getInfo->id,
                'api_token'   => Str::random(60),
                'uuid'        => Str::uuid(),
            ]);
        }
        return $user;
    }
}
