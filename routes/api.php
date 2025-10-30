<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('update', [AuthController::class, 'updateProfile']);
        Route::post('update-avatar', [AuthController::class, 'updateProfileAvatar']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')
    ->prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'indexMe']);
        Route::get('/all', [PostController::class, 'index']);
        Route::get('/{post}', [PostController::class, 'detail']);
        Route::post('/', [PostController::class, 'create']);
        Route::put('{post}', [PostController::class, 'update']);
        Route::delete('{post}', [PostController::class, 'delete']);
        Route::post('{post}/like', [PostController::class, 'likeUnlike']);
        Route::post('{post}/comments', [PostController::class, 'createComment']);
        Route::get('{post}/comments', [PostController::class, 'getComments']);
    });

Route::middleware('auth:sanctum')
    ->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{user}', [UserController::class, 'detail']);
        Route::get('/{user}/posts', [UserController::class, 'getPosts']);
        Route::post('{user}/follow', [UserController::class, 'handleFollow']);
    });
