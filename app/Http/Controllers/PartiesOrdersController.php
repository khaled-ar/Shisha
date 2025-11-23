<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StorePartyOrderRequest;
use App\Models\PartiesOrder;
use Illuminate\Http\Request;

class PartiesOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request('status') ?? "in_delivery";
        return $this->generalResponse(PartiesOrder::latest()->whereStatus($status)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartyOrderRequest $request)
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
    public function update(Request $request, PartiesOrder $parties_order)
    {
        if($parties_order->status == 'pending') {
            $parties_order->forceFill(['status' => 'canceled']);
            $parties_order->save();
            return $this->generalResponse(null);
        }
        return $this->generalResponse(null, 'The order cannot be cancelled as it is in delivery.', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
