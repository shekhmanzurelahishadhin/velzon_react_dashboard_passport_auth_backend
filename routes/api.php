<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\ProjectCategoryController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/forget-password', [LoginController::class, 'forgetPassword']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
Route::middleware('auth:api')->post('/logout', [LoginController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function (){
    Route::apiResource('project-categories',ProjectCategoryController::class);
});
