<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //articles routes
    Route::get('followers/articles', [ArticleController::class, 'fetchFollowingArticles']);
    Route::post('add/articles', [ArticleController::class, 'store']);
    Route::put('update/{article}/articles', [ArticleController::class, 'update']);
    Route::delete('delete/{article}/articles', [ArticleController::class, 'delete']);
    Route::get('clap/{article}/articles', [ArticleController::class, 'articleClap']);
        
});

//articles routes
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::post('find/articles', [ArticleController::class, 'fetchByTerm']);
Route::get('tag/{tag}/articles', [ArticleController::class, 'fetchByTag']);