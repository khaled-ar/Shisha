<?php

use App\Http\Controllers\Admin\PartiesOrdersController;
use Illuminate\Support\Facades\Route;

Route::middleware('employee_parties')->group(function() {
    Route::apiResource('employees/sections/parties-orders', PartiesOrdersController::class)
        ->names('employee.parties');
});


