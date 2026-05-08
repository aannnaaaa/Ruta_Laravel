<?php


use App\Http\Controllers\TripControllerApi;
use App\Http\Controllers\PostControllerApi;
use App\Http\Controllers\ProfileControllerApi;
use App\Http\Controllers\TagControllerApi;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



//http://localhost:8000/api/trips
Route::get('/trips', [TripControllerApi::class, 'index']);
Route::get('/trips/{id}', [TripControllerApi::class, 'show']);

//http://localhost:8000/api/posts
Route::get('/posts', [PostControllerApi::class, 'index']);
Route::get('/posts/{id}', [PostControllerApi::class, 'show']);

////http://localhost:8000/api/profiles
//Route::get('/profiles', [ProfileControllerApi::class, 'index']);
//Route::get('/profiles/{id}', [ProfileControllerApi::class, 'show']);

//http://localhost:8000/api/tags
Route::get('/tags', [TagControllerApi::class, 'index']);
Route::get('/tags/{id}', [TagControllerApi::class, 'show']);

//http://localhost:8000/api/login
Route::post('/login', [AuthController::class, 'login']);

Route::get('/trips_total', [TripControllerApi::class, 'total']);
Route::get('/posts_total', [PostControllerApi::class, 'total']);

Route::middleware ( 'auth:sanctum')->get( '/logout', [AuthController::class, 'logout']);

Route:: group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profiles', [ProfileControllerApi::class, 'index']);
    Route::get('/profiles/{id}', [ProfileControllerApi::class, 'show']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/logout', [AuthController::class, 'logout']);
});
