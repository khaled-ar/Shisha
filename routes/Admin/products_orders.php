<?php

use App\Http\Controllers\Admin\ProductsOrdersController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products-orders', ProductsOrdersController::class)
    ->names('admin.products_orders');
