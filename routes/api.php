<?php

use App\Http\Controllers\Admin\StatisticsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

$base_path = base_path('routes');

// Auth Routes
include "{$base_path}/auth.php";

Route::middleware(['auth:sanctum', 'whatsapp_verified'])->group(function() use($base_path) {

    // User Routes
    include "{$base_path}/user_routes.php";

    // Employee Products Routes
    include "{$base_path}/employee_products_routes.php";

    // Employee Parties Routes
    include "{$base_path}/employee_parties_routes.php";

    // Admin Routes
    $admin_path = "{$base_path}/Admin";
    Route::prefix('admin')->middleware('admin')->group(function() use($admin_path) {
        include "{$admin_path}/stores.php";
        include "{$admin_path}/employees.php";
        include "{$admin_path}/categories.php";
        include "{$admin_path}/users.php";
        include "{$admin_path}/products.php";
        include "{$admin_path}/ads.php";
        include "{$admin_path}/prices.php";
        include "{$admin_path}/parties_orders.php";
        Route::get('statistics', [StatisticsController::class, 'index']);
    });
});
