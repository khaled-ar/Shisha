<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StorePartyOrderRequest;
use App\Models\PartiesOrder;
use App\Models\Price;
use Illuminate\Http\Request;

class PartiesOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request('status');
        if($status) {
            return $this->generalResponse(request()->user()->parties_orders()->latest()->whereStatus($status)->get());
        }
        return $this->generalResponse(request()->user()->parties_orders()->latest()->get());

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
    public function show(PartiesOrder $parties_order)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartiesOrder $parties_order)
    {
        if($request->has('re_order') && $request->re_order == 1) {
            $parties_order->forceFill(['status' => 'pending']);
            $parties_order->save();
            return $this->generalResponse(null);
        }

        if($parties_order->status == 'pending') {
            $prices = Price::pluck('price', 'object')->toArray();
            $updated = [];
            if($request->hookahs) {
                $updated['hookahs'] = $request->hookahs;
            }
            if($request->hours) {
                $updated['hours'] = $request->hours;
            }
            if($request->persons) {
                $updated['persons'] = $request->persons;
            }
            if($request->delivery_cost) {
                $updated['delivery_cost'] = $request->delivery_cost;
            }
            if($request->lon && $request->lat) {
                $updated['lon'] = $request->lon;
                $updated['lat'] = $request->lat;
            }
            if($updated) {
                if($parties_order->update($updated)) {
                    $parties_order->update(['total' => $prices['single_hookah'] * $parties_order->hookahs
                        + $prices['single_hour'] * $parties_order->hours
                        + $prices['single_person'] * $parties_order->persons
                        + $parties_order->delivery_cost
                    ]);
                }
                return $this->generalResponse(null, 'Updated Successfully');
            }

            $parties_order->forceFill(['status' => 'canceled']);
            $parties_order->save();
            return $this->generalResponse(null);
        }
        return $this->generalResponse(null, 'The order cannot be updated as it is in delivery.', 400);
    }

    public function confirm(Request $request, PartiesOrder $parties_order)
    {
        if($parties_order->status == 'pending') {
            $parties_order->forceFill(['status' => 'confirmed']);
            $parties_order->save();
            return $this->generalResponse(null);
        }
        return $this->generalResponse(null, 'The order cannot be updated as it is in delivery.', 400);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
