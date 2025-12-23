<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('driver')->group(function() {
    Route::patch('driver/update-work-status', function(Request $request) {
        $request->user()->employee()->update([
            'work_status' => $request->work_status
        ]);
        return response()->json([
            'message' => null,
            'data' => null
        ]);
    })
    ->name('driver.work_status');
});


