<?php

use App\Http\Controllers\Admin\EmployeesController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->apiResource('employees', EmployeesController::class);
