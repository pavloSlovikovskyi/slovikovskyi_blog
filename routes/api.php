<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FrontControllersPosts\PostController;
use App\Http\Controllers\Api\FrontControllersPosts\PostUiController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('blog/posts', [PostController::class, 'index']);

Route::get('blog/posts-ui', [PostUiController::class, 'index']);

Route::get('/blog/posts/{id}', [PostController::class, 'show']);

Route::apiResource('categories', CategoryController::class);

Route::apiResource('posts', PostController::class);

Route::get('users', [App\Http\Controllers\Api\UserController::class, 'index']);
