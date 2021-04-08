<?php

namespace App\Http\Controllers;

use App\Article;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $articles = Article::with([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->with(['categories' => function($query){
                                        $query->select('categories.id', 'categories.name')->get();
                                    }
                                ])->withCount('likes')->with([
            'likes' => function($query){
                $query->where('user_id', auth()->user()->id);
            }
        ])->Where('user_id', Auth::user()->id)->orderBy('id', 'asc')->get();

        
        return $articles;
    }

    public function picture(Request $request)
    {
        $image = $request->file('picture');
        $normal = Image::make($image)->resize(200, 160);
        $name = substr($image->store('pictures'), strrpos($image->store('pictures'), '/') + 1);
        
        $pathFolder = 'profil/'.auth()->user()->id;
        Storage::disk('s3')->deleteDirectory($pathFolder);

        $path = Storage::disk('s3')->put("$pathFolder/$name", $normal->stream()->__toString());
        $url = Storage::disk('s3')->url("$pathFolder/$name");

        $profil = User::where('id', Auth::user()->id)->update(['image_url' => $url]);
        return $url;
    }

    public function infoUser()
    {
        $me = User::with([
            'favorites' => function($query){
                $query->where('favorites.user_id', auth()->user()->id);
            }
        ])->Where('id', auth()->user()->id)->first();


        $articles = Article::with([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->with(['categories' => function($query){
                                        $query->select('categories.id', 'categories.name')->get();
                                    }
                                ])->withCount('likes')
                        ->with([
                            'likes' => function($query){
                                $query->where('user_id', auth()->user()->id);
                            }
                        ])->Where('user_id', Auth::user()->id)->orderBy('id', 'asc')->count();
        
        $followings = $me->followings;
        $following = array();
        foreach($followings as $k=>$user)
        {
            $u = User::find($user['id']);
            if(!$me->hasRequestedToFollow($u) and !$me->isBlocked($user->id) and !$u->isBlocked($me->id))
                $following[] = $user;
        }

        $followers = $me->followers;
        $follower = array();
        foreach($followers as $k=>$user)
        {
            $u = User::find($user['id']);
            if(!$me->isBlocked($user->id) and !$u->isBlocked($me->id))
                $follower[] = $user;
        }

        $me['nbr_friends'] = count($following);
        $me['nbr_abo'] =  count($follower);
        $me['nbr_articles'] = $articles;

        return $me;
    }

    public function privacy()
    {
        $me = User::find(auth()->user()->id);

        User::where('id', $me->id)->update(['private' => $me->private == 1 ? 0 : 1 ]);
    }

    public function description(Request $request)
    {
        $me = User::find(auth()->user()->id);

        User::where('id', $me->id)->update(['description' => $request->input('description') ]);
    }

}
