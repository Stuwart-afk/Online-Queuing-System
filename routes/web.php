<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\PhoneVerificationController;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tracking', function () {
    return view('Tracking');
});
Route::get('/cashier-login', function () {
    return view('cashier-login');
});
Route::get('/cashier1', function () {
    return view('cashier1');
});

Volt::route('/cashier', 'cashier-dashboard');

Volt::route('/queue/status/{token}', 'show')
    ->name('queue.status');

Route::post('/submit', [UserControllers::class, 'store'])
    ->name('submit.form');

Route::get('/queue/check-device', [UserControllers::class, 'checkDevice']);


Route::get('/verify-phone', [PhoneVerificationController::class, 'show'])
    ->name('phone.verify');
Route::post('/verify-phone', [PhoneVerificationController::class, 'verifyOtp'])
    ->name('phone.verify.submit');
Route::post('/verify-phone/resend', [PhoneVerificationController::class, 'resendOtp'])
    ->name('verify.phone.resend');


Route::get('/contact', function () {
    return view('contact');
});

Route::get('/about', function () {
    return view('about');
});
