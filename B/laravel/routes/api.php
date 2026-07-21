<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HashtagController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])
    ->middleware('auth.token');

Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth.token');
Route::put('/profile', [ProfileController::class, 'update'])
    ->middleware('auth.token');
Route::get('/users/{username}', [ProfileController::class, 'showPublic']);
Route::get('/users/{username}/posts', [PostController::class, 'creatorPosts'])
    ->middleware('auth.token.optional');

Route::get('/posts', [PostController::class, 'index'])
    ->middleware('auth.token.optional');
Route::post('/posts', [PostController::class, 'store'])
    ->middleware('auth.token');
Route::get('/posts/{id}', [PostController::class, 'show'])
    ->whereNumber('id')
    ->middleware('auth.token.optional');
Route::put('/posts/{id}', [PostController::class, 'update'])
    ->whereNumber('id')
    ->middleware('auth.token');
Route::delete('/posts/{id}', [PostController::class, 'destroy'])
    ->whereNumber('id')
    ->middleware('auth.token');

Route::post('/posts/{id}/like', [LikeController::class, 'store'])
    ->whereNumber('id')
    ->middleware('auth.token');
Route::delete('/posts/{id}/like', [LikeController::class, 'destroy'])
    ->whereNumber('id')
    ->middleware('auth.token');
Route::get('/posts/{id}/likes', [LikeController::class, 'index'])
    ->whereNumber('id');

Route::get('/hashtags/trending', [HashtagController::class, 'trending']);
Route::get('/hashtags/{name}/posts', [HashtagController::class, 'posts'])
    ->middleware('auth.token.optional');
