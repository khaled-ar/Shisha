<?php

use App\Http\Controllers\Admin\AdsController;
use Illuminate\Support\Facades\Route;

Route::prefix('ads')->apiResource('ads', AdsController::class);
