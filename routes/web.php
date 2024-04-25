<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FlightSearchController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes([
    'login' => true,
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/live/city/airport/search', [HomeController::class, 'liveCityAirportSearch'])->name('LiveCityAirportSearch');

Route::post('/search/flights', [FlightSearchController::class, 'searchFlights'])->name('SearchFlights');
Route::get('/flight/search-results', [FlightSearchController::class, 'showFlightSearchResults'])->name('ShowFlightSearchResults');
Route::post('/price/range/filter', [FlightSearchController::class, 'priceRangeFilter'])->name('PriceRangeFilter');
Route::get('/clear/price/range/filter', [FlightSearchController::class, 'clearPriceRangeFilter'])->name('ClearPriceRangeFilter');
