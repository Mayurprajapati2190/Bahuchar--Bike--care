<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BikeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\StaffUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/our-services', [MarketingController::class, 'services'])->name('marketing.services');
Route::get('/why-us', [MarketingController::class, 'whyUs'])->name('marketing.why-us');
Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/pending/list', [BillController::class, 'pending'])->name('bills.pending');
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::patch('bills/{bill}/payment', [BillController::class, 'updatePayment'])->name('bills.update-payment');
    Route::delete('bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');

    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/bikes', [BikeController::class, 'store'])->name('customers.bikes.store');
    Route::delete('customers/{customer}/bikes/{bike}', [BikeController::class, 'destroy'])->name('customers.bikes.destroy');

    Route::resource('services', ServiceRecordController::class)
        ->parameters(['services' => 'service']);
    Route::post('services/{service}/complete', [ServiceRecordController::class, 'complete'])->name('services.complete');
    Route::post('services/{service}/send-reminder', [ServiceRecordController::class, 'sendReminder'])->name('services.send-reminder');
});

Route::middleware(['auth', 'super-admin'])->group(function () {
    Route::get('/staff', [StaffUserController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffUserController::class, 'store'])->name('staff.store');
    Route::delete('/staff/{user}', [StaffUserController::class, 'destroy'])->name('staff.destroy');

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
});
