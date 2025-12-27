<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PartiesOrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductsOrdersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('driver')->prefix('driver')->group(function() {
    Route::patch('update-work-status', function(Request $request) {
        $request->user()->employee()->update([
            'work_status' => $request->work_status
        ]);
        return response()->json([
            'message' => null,
            'data' => null
        ]);
    })
    ->name('driver.work_status');
    Route::get('get-work-status', function(Request $request) {
        return response()->json([
            'message' => null,
            'data' => $request->user()->employee->work_status
        ]);
    })
    ->name('driver.get.work_status');

    Route::controller(ProductsOrdersController::class)->group(function() {
        Route::get('available-products-orders', 'get_available_for_driver');
        Route::get('get-user-orders-details', 'get_user_orders_details');
        Route::patch('approve-user-orders', 'approve_user_orders');
    });

    Route::controller(PartiesOrdersController::class)->group(function() {
        Route::get('get-parties-orders', 'get_parties_orders');
    });
});


