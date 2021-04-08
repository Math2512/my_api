<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class Vue
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        
        if(!$token){
            return response()->json(['message'=>'Identifants Introuvable #403'], 403);
        }
        $user = User::Where('api_token', $token)->first();
        
        if(!$user){
            return response()->json(['message'=>$token], 403);
        }
        Auth::login($user);
        return $next($request);
    }
}
