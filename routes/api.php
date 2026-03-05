<?php


use App\Http\Controllers\TripControllerApi;
use App\Http\Controllers\PostControllerApi;
use App\Http\Controllers\ProfileControllerApi;
use App\Http\Controllers\TagControllerApi;
use Illuminate\Support\Facades\Route;



//http://localhost:8000/api/trips
Route::get('/trips', [TripControllerApi::class, 'index']);
Route::get('/trips/{id}', [TripControllerApi::class, 'show']);

//http://localhost:8000/api/posts
Route::get('/posts', [PostControllerApi::class, 'index']);
Route::get('/posts/{id}', [PostControllerApi::class, 'show']);

//http://localhost:8000/api/profiles
Route::get('/profiles', [ProfileControllerApi::class, 'index']);
Route::get('/profiles/{id}', [ProfileControllerApi::class, 'show']);

//http://localhost:8000/api/tags
Route::get('/tags', [TagControllerApi::class, 'index']);
Route::get('/tags/{id}', [TagControllerApi::class, 'show']);

