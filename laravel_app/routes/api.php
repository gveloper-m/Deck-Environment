<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomFieldController;

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
