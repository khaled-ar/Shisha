<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

$base_path = base_path('routes');

// Auth Routes
include "{$base_path}/auth.php";

Route::middleware('auth:sanctum')->group(function() use($base_path) {
    // Admin Routes
    $admin_path = "{$base_path}/Admin";
    Route::prefix('admin')->middleware('admin')->group(function() use($admin_path) {
        // Stores Routes
        include "{$admin_path}/stores.php";
        include "{$admin_path}/employees.php";
    });
});
