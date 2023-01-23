<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DiseasesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
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
Route::get('/activation/{key}', [RegisterController::class, 'activation']);
Route::get('/profile/{key}', [UserController::class, 'index']);
Route::put('/profile', [UserController::class, 'edit']);
Route::get('/diseases', [DiseasesController::class, 'all']);
Route::post('/diseases/post', [DiseasesController::class, 'index']);
Route::post('/survey',[SurveyController::class, 'index']);
Route::post('/reset-password',[ResetPasswordController::class,'index']);
Route::post('/reset-password/verify',[ResetPasswordController::class,'verify']);
Route::post('/reset-password/new-password',[ResetPasswordController::class,'new_password']);