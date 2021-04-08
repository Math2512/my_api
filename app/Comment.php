<?php

namespace App;

use App\User;
use App\Article;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'article_id',
        'comment',
        'parent_id'
    ];

    public function articles(){
        return $this->belongsTo(Article::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
    }

    public function likesComments()
    {
        return $this->belongsToMany(User::class, 'likes_comments', 'comment_id');
    }
}
