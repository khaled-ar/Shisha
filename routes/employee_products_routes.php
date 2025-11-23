<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::middleware('employee_products')->group(function() {
    Route::apiResource('employees/sections/products', ProductsController::class)
        ->names('employee.products');
});


