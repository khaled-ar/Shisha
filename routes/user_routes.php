<?php

use App\Http\Controllers\PartiesOrdersController;
use App\Http\Controllers\ProductsOrdersController;
use App\Http\Controllers\ProfileController;
use App\Models\{
    Ad,
    Category,
    Price,
    Product,
    Store,
};
use Illuminate\Support\Facades\Route;

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
    $store = Store::first();
    return response()->json(['message' => null, 'store_lon' => $store->lon, 'store_lat' => $store->lat, 'data' => Price::all()]);
});

Route::middleware('user')->group(function() {

});

Route::middleware(['auth:sanctum', 'user', 'whatsapp_verified'])->group(function() {

    Route::controller(ProfileController::class)->group(function() {
        Route::get('profile', 'show');
        Route::post('profile', 'update');
    });

    Route::apiResource('products-orders', ProductsOrdersController::class);
    Route::post('products-orders/confirm', [ProductsOrdersController::class, 'confirm']);
    Route::apiResource('parties-orders', PartiesOrdersController::class);
    Route::post('parties-orders/confirm/{parties_order}', [PartiesOrdersController::class, 'confirm']);

});


