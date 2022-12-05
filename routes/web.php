<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RegisterController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/uploads', [UploadController::class, 'index']);
Route::post('/article/post', [ArticleController::class, 'index']);
Route::get('/article', [ArticleController::class, 'all']);
Route::get('/article/{key}', [ArticleController::class, 'getArticleById']);
Route::post('/register', [RegisterController::class, 'index']);
Route::post('/login', [LoginController::class, 'index']);
Route::get('/results/{key}', [ResultController::class, 'index']);

// Route::get("/activation?{key}", [UserController::class, 'activation']);