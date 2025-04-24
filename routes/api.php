<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthenticationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('user/registration', [AuthenticationController::class, 'userRegistration']);
Route::post('user/verification', [AuthenticationController::class, 'userVerification']);
Route::post('user/login', [AuthenticationController::class, 'userLogin']);
Route::post('/forgot/password', [AuthenticationController::class, 'forgotPassword']);
Route::post('/reset/password', [AuthenticationController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout']);
});

