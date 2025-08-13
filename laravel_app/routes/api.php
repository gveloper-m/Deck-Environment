<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayPalController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register-admin', [AuthController::class, 'registerAdmin']);

Route::get('/available-custom-fields', [CustomFieldController::class, 'availableFields']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // User Account
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/user', [AuthController::class, 'deleteUser']);
    Route::put('/user/update', [AuthController::class, 'updateUser']);
    Route::get('/user-info', [AuthController::class, 'getAllInfo']);

    // Custom Fields
    Route::get('/custom-fields', [CustomFieldController::class, 'index']);
    Route::post('/custom-fields', [CustomFieldController::class, 'store']);
    Route::delete('/custom-fields/{field_name}', [CustomFieldController::class, 'destroy']);
    Route::get('/custom-fields/{id}', [CustomFieldController::class, 'getByUserId']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/user/{id}', [AuthController::class, 'getUserById']);

    // Example admin dashboard route (uncomment if needed)
    // Route::get('/admin/dashboard', function () {
    //     return 'Welcome Admin!';
    // });
});

/*
|--------------------------------------------------------------------------
| User routes (must be logged in)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/my-meetings', [MeetingController::class, 'myMeetings']);
    Route::get('/my-meetings/past', [MeetingController::class, 'myPastMeetings']);
    Route::get('/my-meetings/future', [MeetingController::class, 'myFutureMeetings']);
    Route::put('/edit-meeting/{id}', [MeetingController::class, 'update']);
    Route::delete('/delete-meeting/{id}', [MeetingController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/meetings', [MeetingController::class, 'adminAllMeetings']);
    Route::get('/admin/meetings/user/{userId}', [MeetingController::class, 'adminMeetingsByUser']);
    Route::get('/admin/meetings/past', [MeetingController::class, 'adminPastMeetings']);
    Route::get('/admin/meetings/future', [MeetingController::class, 'adminFutureMeetings']);
    Route::put('/admin/edit-meeting/{id}', [MeetingController::class, 'adminUpdateMeeting']);
    Route::delete('/admin/delete-meeting/{id}', [MeetingController::class, 'adminDeleteMeeting']);
});

// Admin routes
 Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/payments', [PaymentController::class, 'index']);
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-payments', [PaymentController::class, 'myPayments']);
    Route::post('/payments', [PaymentController::class, 'store']);
});


Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
