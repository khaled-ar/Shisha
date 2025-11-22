<?php

use App\Http\Controllers\Admin\PricesController;
use Illuminate\Support\Facades\Route;

Route::prefix('prices')->apiResource('prices', PricesController::class);
