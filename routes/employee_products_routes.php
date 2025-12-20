<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::middleware('employee_products')->group(function() {
    Route::get('employees/sections/products/categories', [CategoriesController::class, 'index'])
        ->name('employee.categories');

    Route::apiResource('employees/sections/products', ProductsController::class)
        ->names('employee.products');
});


