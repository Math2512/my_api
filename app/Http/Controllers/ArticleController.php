<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Article;
use App\Category;
use Illuminate\Http\Request;
use Dusterio\LinkPreview\Client;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AlertesNotification;

class ArticleController extends Controller
{

    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        $followings = $user->followings;
        $users[] = auth()->user()->id;

        foreach($followings as $following)
        {
            $u = User::find($following['id']);
            if(!$user->hasRequestedToFollow($u) and !$user->isBlocked($following->id) and !$following->isBlocked($user->id))
               $users[] = $following['id'];
        }
        
        $articles[] = Article::with(['user' => function($query){
                                        $query->select('id','uuid', 'name', 'image_url')->get();
                                    }
                                ])
                        ->with(['categories' => function($query){
                                        $query->select('categories.id', 'categories.name')->get();
                                    }
                                ])
                        ->withCount('likes')
                        ->withCount('comments')
                        ->with([
                            'likes' => function($query){
                                $query->where('user_id', auth()->user()->id);
                            }
                        ])
                        ->with([
                            'comments' => function($query){
                                $query->with([
                                    'user'=> function($query){
                                        $query->select('id','uuid', 'name', 'image_url')->get();
                                    }
                                ]);
                            }
                        ])
                        ->orderBy('id', 'desc')->WhereIn('user_id', $users)->paginate(10);
       
        
        return $articles[0];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required|max:255',
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return $message;
        }
        
        
        $previewClient = new Client($request->input('link'));
        $previews = $previewClient->getPreviews();
        $preview = $previewClient->getPreview('general');
        
        $cover = $preview->toArray();
        $url = null;
        
        if($cover['cover']){
            $image = $cover['cover'];
            
            try {
                $current = file_get_contents($image);
                $name = substr($image, strrpos($image, '/') + 1);

                $normal = Image::make($current)->resize(300, 200);
                //Storage::put($name, $current);
                //10.25
                $path = Storage::disk('s3')->put("preview/$name", $normal->stream()->__toString());
                $url = Storage::disk('s3')->url("preview/$name");
            } catch (Exception $e) {
                echo 'Lecture preview impossible', "\n";
            }
        }

        $article = Article::create([
            'name'=>$request->input('name'),
            'link'=>$request->input('link'),
            'user_id'=>Auth::user()->id,
            'preview'=>$url,
            'description'=>'text'
        ]);

        if($request->input('categories')){
        foreach($request->input('categories') as $category)
        {
            $article->categories()->attach($category);
        }}

        return $this->index();

    }

    public function like(Request $request)
    {
        $me = User::find(Auth::user()->id);
        $article = Article::where('id', $request->input('article_id'))
                        ->With([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->first();

        $user = User::find($article['user']['id']);
        $details = [
            'u_name'     => "$me->name",
            'body'       => "a aimé votre article",
            'infos_user' => 'test',
            'u_id'       => $me->uuid,
            'elem_id'    => $article['id'],
        ];

        $user->notify(new AlertesNotification($details));

        auth()->user()->likes()->attach($request->input('article_id'));
    }

    public function dislike(Request $request)
    {
        auth()->user()->likes()->detach($request->input('article_id'));
    }

    public function addFavorite(Request $request)
    {

        auth()->user()->favorites()->attach($request->input('article_id'));
        
    }

    public function unFavorite(Request $request)
    {
        auth()->user()->favorites()->detach($request->input('article_id'));
        
    }

    public function favorites()
    {
        $fav = User::with([
            'favorites' => function($query){
                $query->with('user')->where('favorites.user_id', auth()->user()->id);
            }
        ])->Where('id', auth()->user()->id)->first();
        
        $user = User::where('id', Auth::user()->id)->first();
        
        $favorites = array();
        foreach($fav['favorites'] as $favoris)
        {
            $u = User::find($favoris['user_id']);
            if(!$user->hasRequestedToFollow($u) and !$user->isBlocked($u->id) and !$u->isBlocked($user->id))
                $favorites[] = $favoris;
        }

        return $favorites;
    }
}
