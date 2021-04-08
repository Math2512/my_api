<?php

namespace App;

use App\Article;
use App\Comment;
use Overtrue\LaravelFollow\Followable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, Followable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'image_url', 'description','uuid', 'provider', 'provider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class)->orderBy('created_at', 'DESC');
    }

    public function likes()
    {
        return $this->belongsToMany(Article::class, 'likes', 'user_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites', 'user_id');
    }

    public function isBlocked($userId)
    {
        return (boolean) $this->blockedUsers()
            ->where('user_id', $userId)->count();
    }
    
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'blocked_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
    }

    public function likesComments()
    {
        return $this->belongsToMany(Comment::class, 'likes_comments', 'user_id');
    }

    
}
