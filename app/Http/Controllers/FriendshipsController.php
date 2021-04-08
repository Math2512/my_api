<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AlertesNotification;

class FriendshipsController extends Controller
{

    public function index()
    {
        $me = User::where('id', Auth::user()->id)->first();
        $user = User::where('id', '<>', Auth::user()->id)->groupBy('id')->get();
    }

    public function users()
    {
        $me = User::find(Auth::user()->id);
        $users = User::where('id', '<>', Auth::user()->id)
            ->groupBy('id')->get();
        
        $user = User::find(1);

        // Si $user est bloqué par moi
        //return $user->isBlocked($me->id);

        $utilisateurs = [];
        foreach($users as $k=>$user)
        {
            if(!$me->isBlocked($user->id)){
                $utilisateurs[] = $user;
                $u = User::find($user['id']);
                if($me->hasRequestedToFollow($u))
                    $utilisateurs[$k]['status'] = 1;
                elseif($me->isFollowing($u))
                    $utilisateurs[$k]['status'] = 2;
                else
                    $utilisateurs[$k]['status'] = 0;
                
                if($me->isBlocked($user['id']))
                    $utilisateurs[$k]['is_block'] = 1;
                else
                    $utilisateurs[$k]['is_block'] = 0;

            }
            
        }

        return $utilisateurs;
    }

    public function follow(string $uid)
    {
        $me = User::find(Auth::user()->id);
        $user = User::firstWhere('uuid', $uid);

        $me->follow($user);
        
        if($me->hasRequestedToFollow($user))
            $user['status'] = 1;
        elseif($me->isFollowing($user)){
            $user['status'] = 2;
        }
        else{
            $user['status'] = 0;
        }
        
        $details = [
            'u_name' => "$me->name",
            'body' => "a demandé à vous suivre",
            'infos_user' => $me->image_url,
            'u_id'    => $me->uuid,
            'elem_id'    => "",
        ];

        $user->notify(new AlertesNotification($details));

    }

    public function followings()
    {
        $me = User::find(Auth::user()->id);
        
        $followings = $me->followings;
        $following = array();
        foreach($followings as $k=>$user)
        {
            $u = User::find($user['id']);
            if(!$me->hasRequestedToFollow($u) and !$me->isBlocked($user->id))
                $following[] = $user;
        }

        return $following;
    }

    public function followers()
    {
        $me = User::find(Auth::user()->id);

        $followers = $me->followers;

        
        $utilisateurs = [];
        foreach($followers as $k=>$user)
        {
            
            if(!$me->isBlocked($user->id)){
                $utilisateurs[] = $user;
                $u = User::find($user['id']);
                if($me->hasRequestedToFollow($u))
                    $utilisateurs[$k]['status'] = 1;
                elseif($me->isFollowing($u)){
                    $utilisateurs[$k]['status'] = 2;
                }
                else{
                    $utilisateurs[$k]['status'] = 0;
                }

                if($me->isBlocked($user['id']))
                    $utilisateurs[$k]['is_block'] = 1;
                else
                    $utilisateurs[$k]['is_block'] = 0;
            }

        }

        return $utilisateurs;
    }

    public function unfollow(string $uid)
    {
        $user1 = User::find(Auth::user()->id);
        $user2 = User::firstWhere('uuid', $uid);

        $user1->unfollow($user2);
    }

    public function removefollower(string $uid)
    {
        $user1 = User::find(Auth::user()->id);
        $user2 = User::firstWhere('uuid', $uid);

        $user1->rejectFollowRequestFrom($user2);
        
    }

    public function acceptFriendRequest(string $uid)
    {
        $user1 = User::find(Auth::user()->id);
        $user2 = User::firstWhere('uuid', $uid);
        $user1->unreadNotifications->markAsRead();

        $user1->acceptFollowRequestFrom($user2);

        $notifs = $user1->notifications;

        return $notifs;
    }
    
    public function denyFriendRequest(string $uid, Request $request)
    {

        $user1 = User::find(Auth::user()->id);
        $user2 = User::firstWhere('uuid', $uid);

        $user1->rejectFollowRequestFrom($user2);

        $user1->notifications()
        ->where('id', $request->input('n_id'))
        ->get()
        ->first()
        ->delete();

        $notifs = $user1->notifications;

        return $notifs;
    }

    
    public function blockUser(string $uid)
    {
        $user = User::firstWhere('uuid', $uid);
        $user->blockedUsers()->attach(Auth::user()->id);
    }

    public function unBlockUser(string $uid)
    {
        $user = User::firstWhere('uuid', $uid);
        $user->blockedUsers()->detach(Auth::user()->id);
    }
    
}
