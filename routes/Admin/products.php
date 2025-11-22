<?php

use App\Http\Controllers\Admin\ProductsController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->apiResource('products', ProductsController::class);
