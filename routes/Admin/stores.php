<?php

use App\Http\Controllers\Admin\StoresController;
use Illuminate\Support\Facades\Route;

Route::prefix('stores')->apiResource('stores', StoresController::class);
