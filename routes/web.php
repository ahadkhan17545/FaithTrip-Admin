<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GdsController;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

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
Route::get('/live/airline/search', [HomeController::class, 'liveAirlineSearch'])->name('LiveAirlineSearch');
Route::post('/passanger/live/search', [HomeController::class, 'passangerLiveSearch'])->name('PassangerLiveSearch');

Route::get('ckeditor', [CkeditorController::class, 'index']);
Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');


Route::group(['middleware' => ['auth']], function () {

    Route::post('/search/flights', [FlightSearchController::class, 'searchFlights'])->name('SearchFlights');
    Route::get('/flight/search-results', [FlightSearchController::class, 'showFlightSearchResults'])->name('ShowFlightSearchResults');
    Route::get('select/flight/{session_index}', [FlightSearchController::class, 'revalidateFlight'])->name('RevalidateFlight');

    // search next and previous
    Route::get('/search/next/day', [FlightSearchController::class, 'searchNextDay'])->name('SearchNextDay');
    Route::get('/search/prev/day', [FlightSearchController::class, 'searchPreviousDay'])->name('SearchPreviousDay');

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

    // flight booking routes
    Route::post('create/pnr/with/booking', [FlightBookingController::class, 'bookFlightWithPnr'])->name('BookFlightWithPnr');
    Route::get('view/all/booking', [FlightBookingController::class, 'viewAllBooking'])->name('ViewAllBooking');
    Route::get('view/cancel/booking', [FlightBookingController::class, 'viewCancelBooking'])->name('ViewCancelBooking');
    Route::get('flight/booking/details/{booking_no}', [FlightBookingController::class, 'flightBookingDetails'])->name('FlightBookingDetails');
    Route::get('cancel/flight/booking/{pnr_id}', [FlightBookingController::class, 'cancelFlightBooking'])->name('CancelFlightBooking');
    Route::get('booking/preview/{pnr_id}', [FlightBookingController::class, 'bookingPreview'])->name('BookingPreview');
    Route::get('issue/flight/ticket/{pnr_id}', [FlightBookingController::class, 'issueFlightTicket'])->name('IssueFlightTicket');
    Route::get('view/issued/tickets', [FlightBookingController::class, 'viewIssuedTickets'])->name('ViewIssuedTickets');
    Route::get('view/cancelled/tickets', [FlightBookingController::class, 'viewCancelledTickets'])->name('ViewCancelledTickets');
    Route::post('update/pnr/booking', [FlightBookingController::class, 'updatePnrBooking'])->name('UpdatePnrBooking');

    // recharge
    Route::get('create/topup/request', [PaymentController::class, 'createTopupRequest'])->name('CreateTopupRequest');
    Route::post('submit/recharge/request', [PaymentController::class, 'submitRechargeRequest'])->name('SubmitRechargeRequest');
    Route::get('view/recharge/requests', [PaymentController::class, 'viewRechargeRequests'])->name('ViewRechargeRequests');
    Route::get('delete/recharge/request/{slug}', [PaymentController::class, 'deleteRechargeRequest'])->name('ViewRechargeRequests');

    // report
    Route::get('flight/booking/report', [ReportController::class, 'flightBookingReport'])->name('FlightBookingReport');
    Route::post('generate/flight/booking/report', [ReportController::class, 'generateFlightBookingReport'])->name('GenerateFlightBookingReport');

    Route::group(['middleware' => ['CheckUserType']], function () {

        // recharge
        Route::get('approve/recharge/request/{slug}', [PaymentController::class, 'approveRechargeRequest'])->name('ApproveRechargeRequest');
        Route::get('deny/recharge/request/{slug}', [PaymentController::class, 'denyRechargeRequest'])->name('DenyRechargeRequest');

        // setup gds routes
        Route::get('setup/gds', [GdsController::class, 'setupGds'])->name('SetupGds');
        Route::post('gds/status/update', [GdsController::class, 'gdsStatusUpdate'])->name('GdsStatusUpdate');
        Route::get('edit/gds/{code}', [GdsController::class, 'editGdsInfo'])->name('EditGdsInfo');
        Route::post('update/sabre/gds/info', [GdsController::class, 'updateSabreGdsInfo'])->name('UpdateSabreGdsInfo');
        Route::post('update/flyhub/gds/info', [GdsController::class, 'updateFlyhubGdsInfo'])->name('UpdateFlyhubGdsInfo');
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

        // bank accounts
        Route::get('view/bank/accounts', [PaymentController::class, 'viewBankAccounts'])->name('ViewBankAccounts');
        Route::get('add/bank/account', [PaymentController::class, 'addBankAccount'])->name('AddBankAccount');
        Route::post('save/bank/account', [PaymentController::class, 'saveBankAccount'])->name('SaveBankAccount');
        Route::get('delete/bank/account/{slug}', [PaymentController::class, 'deleteBankAccount'])->name('DeleteBankAccount');
        Route::get('edit/bank/account/{slug}', [PaymentController::class, 'editBankAccount'])->name('EditBankAccount');
        Route::post('update/bank/account', [PaymentController::class, 'updateBankAccount'])->name('UpdateBankAccount');

        // mfs accounts
        Route::get('view/mfs/accounts', [PaymentController::class, 'viewMfsAccounts'])->name('ViewMfsAccounts');
        Route::get('add/mfs/account', [PaymentController::class, 'addMfsAccount'])->name('AddMfsAccount');
        Route::post('save/mfs/account', [PaymentController::class, 'saveMfsAccount'])->name('SaveMfsAccount');
        Route::get('delete/mfs/account/{slug}', [PaymentController::class, 'deleteMfsAccount'])->name('DeleteMfsAccount');
        Route::get('edit/mfs/account/{slug}', [PaymentController::class, 'editMfsAccount'])->name('EditMfsAccount');
        Route::post('update/mfs/account', [PaymentController::class, 'updateMfsAccount'])->name('UpdateMfsAccount');

        // b2b user management
        Route::get('create/b2b/users', [UserController::class, 'createB2bUser'])->name('CreateB2bUser');
        Route::post('save/b2b/user', [UserController::class, 'saveB2bUser'])->name('SaveB2bUser');
        Route::get('view/b2b/users', [UserController::class, 'viewB2bUser'])->name('ViewB2bUser');
        Route::get('delete/b2b/user/{id}', [UserController::class, 'deleteB2bUser'])->name('DeleteB2bUser');
        Route::get('edit/b2b/user/{id}', [UserController::class, 'editB2bUser'])->name('EditB2bUser');
        Route::post('update/b2b/user', [UserController::class, 'updateB2bUser'])->name('UpdateB2bUser');
        Route::get('view/saved/passangers', [UserController::class, 'savedPassangers'])->name('SavedPassangers');
        Route::get('delete/saved/passanger/{id}', [UserController::class, 'deleteSavedPassanger'])->name('DeleteSavedPassanger');

        // Report
        Route::get('b2b/financial/report', [ReportController::class, 'b2bFinancialReport'])->name('B2bFinancialReport');
        Route::post('generate/b2b/financial/report', [ReportController::class, 'generateB2bFinancialReport'])->name('GenerateB2bFinancialReport');

    });

});

