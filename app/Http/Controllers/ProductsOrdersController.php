<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\{
    StoreProdutsOrderRequest,
    UpdateProdutsOrderRequest,
};
use App\Models\ProductsOrder;
use Illuminate\Http\Request;

class ProductsOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutsOrderRequest $request)
    {
        return $request->store();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutsOrderRequest $request, ProductsOrder $products_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
