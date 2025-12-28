<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PartiesOrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductsOrdersController;
use App\Models\ProductsOrder;
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

    Route::get('can-approve-orders', function(Request $request) {
        return response()->json([
            'message' => null,
            'data' => ProductsOrder::whereEmployeeId(request()->user()->employee->id)->whereStatus('in_delivery')->count() == 0,
        ]);
    })
    ->name('driver.can_approve_orders');

    Route::controller(ProductsOrdersController::class)->group(function() {
        Route::get('available-products-orders', 'get_available_for_driver');
        Route::get('get-user-orders-details', 'get_user_orders_details');
        Route::patch('approve-user-orders', 'approve_user_orders');
        Route::patch('mark-orders-as-delivered', 'mark_orders_as_delivered');
    });

    Route::controller(PartiesOrdersController::class)->group(function() {
        Route::get('get-parties-orders', 'get_parties_orders');
    });
});


