<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->group(function() {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware('auth:sanctum');

    Route::post('forgot-password', 'forgot_password');
    Route::post('reset-password', 'reset_password');
    Route::post('verify-account', 'verify_account');
    Route::post('verify-code', 'verify_code');
    Route::post('resend-code', 'resend_code')->middleware('throttle:1,1');

});
