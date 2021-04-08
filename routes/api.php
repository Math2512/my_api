<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FriendshipsController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GoogleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['App\Http\Middleware\Vue']], function(){

    //Articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);

    //Like
    Route::post( '/articles/like', [ ArticleController::class, 'like']);
    Route::post( '/articles/dislike', [ ArticleController::class, 'dislike']);

    //Favoris
    Route::post( '/articles/favorite', [ ArticleController::class, 'addFavorite']);
    Route::post( '/articles/unfavorite', [ ArticleController::class, 'unFavorite']);
    Route::get( '/articles/favorites', [ ArticleController::class, 'favorites']);
    
    //Commentaires
    Route::post( '/comments/like', [ CommentController::class, 'like']);
    Route::post( '/comments/dislike', [ CommentController::class, 'dislike']);
    Route::post( '/comments/{id}', [ CommentController::class, 'addCommentaire']);
    Route::get( '/comments/{id}', [ CommentController::class, 'showCommentaires']);

    //Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);

    //Système Follow
    Route::get('/users', [FriendshipsController::class, 'users']);
    Route::get('/followers', [FriendshipsController::class, 'followers']);
    Route::get('/followings', [FriendshipsController::class, 'followings']);

    Route::post('/follow/{id}', [FriendshipsController::class, 'follow']);
    Route::post('/unfollow/{id}', [FriendshipsController::class, 'unfollow']);

    Route::post('/accept/{id}', [FriendshipsController::class, 'acceptFriendRequest']);
    Route::post('/deny/{id}', [FriendshipsController::class, 'denyFriendRequest']);

    Route::post('/block/{id}', [FriendshipsController::class, 'blockUser']);
    Route::post('/unblock/{id}', [FriendshipsController::class, 'unBlockUser']);

    Route::get('/friends/request/{id}', [FriendshipsController::class, 'hasSentFriendRequestTo']);
    Route::post('/remove/friends/{id}', [FriendshipsController::class, 'removeFriend']);
    Route::post('/remove/follower/{id}', [FriendshipsController::class, 'removefollower']);

    //Profil
    Route::get('/me/profil', [ProfilController::class, 'infoUser']);
    Route::get('/profil', [ProfilController::class, 'index']);
    Route::post('/profil/description', [ProfilController::class, 'description']);
    Route::post('/profil/photo/update', [ProfilController::class, 'picture']);
    Route::post('/profil/privacy/update', [ProfilController::class, 'privacy']);

    //alertes
    Route::get('/alert', [AlertController::class, 'index']);

});

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::get('/auth/redirect/{provider}', [GoogleController::class, 'redirect']);

Route::get('/callback/{provider}', [GoogleController::class, 'callback']);