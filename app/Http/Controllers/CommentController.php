<?php

namespace App\Http\Controllers;

use App\Article;
use App\User;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AlertesNotification;

class CommentController extends Controller
{
    public function addCommentaire(int $id, Request $request)
    {
        $me = User::find(Auth::user()->id);
        $article = Article::where('id', $id)
                        ->With([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->first();
        $user = User::find($article['user']['id']);

        Comment::create([
            'comment'   =>$request->input('comment'),
            'user_id'   =>Auth::user()->id,
            'article_id'=>$id,
            'parent_id' =>$request->input('parent_id')
        ]);

        
        $details = [
            'u_name' => "$me->name",
            'u_id' =>  "$me->uuid",
            'body' => "a commenté votre article",
            'infos_user' => $me->image_url,
            'elem_id'    => $article['id']
        ];

        $user->notify(new AlertesNotification($details));
    }

    public function getCommentaire(int $id)
    {
        $comments_by_id = array();
        $comments = Comment::withCount('likesComments')
                            ->with([
                                'likesComments' => function($query){
                                    $query->select('uuid')->where('user_id', auth()->user()->id)->get();
                                }
                            ])->With([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->Where('article_id', $id)->get()->toArray();

        foreach ($comments as $comment) {
            $comments_by_id[$comment['id']] = $comment;
        }

        return $comments_by_id;
    }

    public function showCommentaires($id, $unset_children = true)
    {
        $comments = $this->getCommentaire($id);

        foreach ($comments as $id => $comment) {
            if ($comment['parent_id'] != NULL) {
                $comments[$comment['parent_id']]['children'][] = $comment;

                if ($unset_children) {
                    unset($comments[$id]);
                }
            }
        }

        return $comments;
    }

    public function like(Request $request)
    {
        $me = User::find(Auth::user()->id);

        $comment = Comment::where('id', $request->input('comment_id'))
                        ->With([
                            'user' => function($query){
                                $query->select('id','uuid', 'name', 'image_url')->get();
                            }
                        ])->first();
        $user = User::find($comment['user']['id']);

        $details = [
            'u_name' => "$me->name",
            'u_id' =>  "$me->uuid",
            'body' => "a aimé votre comentaire",
            'infos_user' => $me->image_url,
            'elem_id'    => $comment['id']
        ];
        
        $user->notify(new AlertesNotification($details));

        auth()->user()->likesComments()->attach($request->input('comment_id'));

    }

    public function dislike(Request $request)
    {
        auth()->user()->likesComments()->detach($request->input('comment_id'));
    }
}
