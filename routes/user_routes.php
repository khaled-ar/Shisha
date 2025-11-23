<?php

use App\Http\Controllers\PartiesOrdersController;
use App\Http\Controllers\ProductsOrdersController;
use App\Http\Controllers\ProfileController;
use App\Models\{
    Ad,
    Category,
    Price,
    Product,
};
use Illuminate\Support\Facades\Route;

Route::middleware('user')->group(function() {
    Route::get('ads', function() {
        return response()->json(['message' => null, 'data' => Ad::latest()->paginate(5)]);
    });

    Route::get('categories', function() {
        return response()->json(['message' => null, 'data' => Category::latest()->with('children')->get()]);
    });

    Route::get('products', function() {
        return response()->json(['message' => null, 'data' => Product::latest()->limit(10)->get()]);
    });

    Route::get('parties-prices', function() {
        return response()->json(['message' => null, 'data' => Price::all()]);
    });

    Route::controller(ProfileController::class)->group(function() {
        Route::get('profile', 'show');
        Route::post('profile', 'update');
    });

    Route::apiResource('products-orders', ProductsOrdersController::class);
    Route::apiResource('parties-orders', PartiesOrdersController::class);

});


