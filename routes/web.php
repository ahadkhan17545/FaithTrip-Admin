<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GdsController;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\FlightBookingController;

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

Route::get('ckeditor', [CkeditorController::class, 'index']);
Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');


Route::group(['middleware' => ['auth']], function () {
    Route::post('/search/flights', [FlightSearchController::class, 'searchFlights'])->name('SearchFlights');
    Route::get('/flight/search-results', [FlightSearchController::class, 'showFlightSearchResults'])->name('ShowFlightSearchResults');
    Route::get('select/flight/{session_index}', [FlightSearchController::class, 'revalidateFlight'])->name('RevalidateFlight');

    // filter routes
    Route::post('/price/range/filter', [FlightSearchController::class, 'priceRangeFilter'])->name('PriceRangeFilter');
    Route::get('/clear/price/range/filter', [FlightSearchController::class, 'clearPriceRangeFilter'])->name('ClearPriceRangeFilter');
    Route::post('/airline/carrier/filter', [FlightSearchController::class, 'airlineCarrierFilter'])->name('AirlineCarrierFilter');
    Route::get('/clear/airline/carrier/filter', [FlightSearchController::class, 'clearAirlineCarrierFilter'])->name('ClearAirlineCarrierFilter');

    // company profile routes
    Route::get('/company/profile', [ProfileController::class, 'companyProfile'])->name('CompanyProfile');
    Route::post('/update/company/profile', [ProfileController::class, 'updateCompanyProfile'])->name('UpdateCompanyProfile');
    Route::get('/remove/company/logo', [ProfileController::class, 'removeCompanyLogo'])->name('RemoveCompanyLogo');

    // user profile routes
    Route::get('/my/profile', [ProfileController::class, 'myProfile'])->name('MyProfile');
    Route::post('update/user/profile', [ProfileController::class, 'updateProfile'])->name('UpdateProfile');
    Route::get('/remove/user/image', [ProfileController::class, 'removeUserImage'])->name('RemoveUserImage');

    // setup gds routes
    Route::get('setup/gds', [GdsController::class, 'setupGds'])->name('SetupGds');
    Route::post('gds/status/update', [GdsController::class, 'gdsStatusUpdate'])->name('GdsStatusUpdate');
    Route::get('edit/gds/{code}', [GdsController::class, 'editGdsInfo'])->name('EditGdsInfo');
    Route::post('update/sabre/gds/info', [GdsController::class, 'updateSabreGdsInfo'])->name('UpdateSabreGdsInfo');
    Route::get('view/excluded/airlines', [GdsController::class, 'viewExcludedAirlines'])->name('ViewExcludedAirlines');
    Route::post('save/excluded/airline', [GdsController::class, 'saveExcludedAirline'])->name('SaveExcludedAirline');
    Route::get('delete/excluded/airline/{id}', [GdsController::class, 'deleteExcludedAirline'])->name('DeleteExcludedAirline');
    Route::get('excluded/airline/info/{id}', [GdsController::class, 'excludedAirlineInfo'])->name('ExcludedAirlineInfo');

    // system route for sms & email
    Route::get('/setup/sms/gateways', [SystemController::class, 'viewSmsGateways'])->name('ViewSmsGateways');
    Route::post('/update/sms/gateway/info', [SystemController::class, 'updateSmsGatewayInfo'])->name('UpdateSmsGatewayInfo');
    Route::get('/change/gateway/status/{provider}', [SystemController::class, 'changeGatewayStatus'])->name('ChangeGatewayStatus');
    Route::get('/view/email/config', [SystemController::class, 'viewEmailConfig'])->name('ViewEmailConfig');
    Route::post('/update/email/config', [SystemController::class, 'updateEmailConfig'])->name('UpdateEmailConfig');

    // flight booking routes
    Route::post('create/pnr/with/booking', [FlightBookingController::class, 'bookFlightWithPnr'])->name('BookFlightWithPnr');
    Route::get('view/all/booking', [FlightBookingController::class, 'viewAllBooking'])->name('ViewAllBooking');
    Route::get('view/cancel/booking', [FlightBookingController::class, 'viewCancelBooking'])->name('ViewCancelBooking');
    Route::get('flight/booking/details/{booking_no}', [FlightBookingController::class, 'flightBookingDetails'])->name('FlightBookingDetails');
    Route::get('cancel/flight/booking/{pnr_id}', [FlightBookingController::class, 'cancelFlightBooking'])->name('CancelFlightBooking');

});

