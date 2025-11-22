<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Category,
    Employee,
    Product,
    User,
};

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index() {
        return $this->generalResponse([
            'employees'             => Employee::count(),
            'employees_products'    => Employee::whereSection('products')->count(),
            'employees_parties'     => Employee::whereSection('parties')->count(),
            'employees_drivers'     => Employee::whereSection('driver')->count(),
            'super_categories'      => Category::whereNull('parent_id')->count(),
            'sup_categories'        => Category::whereNotNull('parent_id')->count(),
            'products'              => Product::count(),
            'users'                 => User::whereRole('user')->count(),
        ]);
    }
}
