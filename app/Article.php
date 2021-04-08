<?php

namespace App;

use App\User;
use App\Comment;
use App\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'link',
        'user_id',
        'preview',
        'description',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'categories_articles');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes', 'article_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'article_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
    }

    
}
