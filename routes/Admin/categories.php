<?php

use App\Http\Controllers\CategoriesController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->apiResource('categories', CategoriesController::class);
