<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\FlightController;

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
    Route::post('/update-profile', [AuthenticationController::class, 'updateProfile']);
    Route::get('/submit/user/delete/request', [AuthenticationController::class, 'submitAccountDeleteRequest']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/book/my/flight', [FlightController::class, 'bookMyFlight']);
    Route::post('/flight/booking/payment', [FlightController::class, 'flightBookingPayment']);
    Route::get('/my/flight/bookings', [FlightController::class, 'myFlightBookings']);
    Route::post('/flight/booking/details', [FlightController::class, 'flightBookingDetails']);
    Route::post('/cancel/flight/booking', [FlightController::class, 'cancelFlightBooking']);
});



