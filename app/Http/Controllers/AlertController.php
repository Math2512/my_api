<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $notifs = $user->notifications;

        return $notifs;
    }

}
