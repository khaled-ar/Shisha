<?php

use App\Http\Controllers\Admin\PartiesOrdersController;
use Illuminate\Support\Facades\Route;

Route::prefix('parties/parties-orders')->apiResource('parties/parties-orders', PartiesOrdersController::class)
    ->names('admin.employee.parties');
