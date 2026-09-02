<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BikeController;
use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\V1\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ServiceRecordController;
use App\Http\Controllers\Api\V1\StaffUserController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\CurrentTeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::post('customer/auth/request-otp', [CustomerAuthController::class, 'requestOtp'])
        ->middleware('throttle:5,1');
    Route::post('customer/auth/verify-otp', [CustomerAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'staff', 'current-team'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::put('current-team', [CurrentTeamController::class, 'update']);

        Route::get('dashboard', DashboardController::class);

        Route::get('customers', [CustomerController::class, 'index']);
        Route::post('customers', [CustomerController::class, 'store']);
        Route::get('customers/{customer}', [CustomerController::class, 'show']);
        Route::put('customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);
        Route::post('customers/{customer}/bikes', [BikeController::class, 'store']);
        Route::delete('customers/{customer}/bikes/{bike}', [BikeController::class, 'destroy']);

        Route::get('services/options', [ServiceRecordController::class, 'createOptions']);
        Route::get('services', [ServiceRecordController::class, 'index']);
        Route::post('services', [ServiceRecordController::class, 'store']);
        Route::get('services/{service}', [ServiceRecordController::class, 'show']);
        Route::put('services/{service}', [ServiceRecordController::class, 'update']);
        Route::post('services/{service}/complete', [ServiceRecordController::class, 'complete']);
        Route::post('services/{service}/send-reminder', [ServiceRecordController::class, 'sendReminder']);
        Route::delete('services/{service}', [ServiceRecordController::class, 'destroy']);

        Route::get('bills', [BillController::class, 'index']);
        Route::get('bills/{bill}', [BillController::class, 'show']);
        Route::get('bills/{bill}/print', [BillController::class, 'print']);
        Route::patch('bills/{bill}/payment', [BillController::class, 'updatePayment']);
        Route::delete('bills/{bill}', [BillController::class, 'destroy']);
    });

    Route::middleware(['auth:sanctum', 'staff', 'current-team', 'super-admin'])->group(function () {
        Route::get('teams', [TeamController::class, 'index']);
        Route::post('teams', [TeamController::class, 'store']);
        Route::put('teams/{team}', [TeamController::class, 'update']);
        Route::delete('teams/{team}', [TeamController::class, 'destroy']);

        Route::get('staff-users', [StaffUserController::class, 'index']);
        Route::post('staff-users', [StaffUserController::class, 'store']);
        Route::delete('staff-users/{user}', [StaffUserController::class, 'destroy']);
    });

    Route::middleware(['auth:sanctum', 'customer'])->prefix('customer')->group(function () {
        Route::post('auth/logout', [CustomerAuthController::class, 'logout']);
        Route::get('profile', [CustomerProfileController::class, 'profile']);
        Route::get('bikes', [CustomerProfileController::class, 'bikes']);
        Route::get('services', [CustomerProfileController::class, 'services']);
        Route::get('bills', [CustomerProfileController::class, 'bills']);
        Route::get('next-service-due', [CustomerProfileController::class, 'nextServiceDue']);
    });
});
