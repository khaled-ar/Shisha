<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductsOrder;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductsOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request('status');
        $store = Store::first();
        if(request('driver_id')) {
            $products_orders = ProductsOrder::whereStatus($status)
                ->whereEmployeeId(request('driver_id'))
                ->whereHas('user')
                ->whereHas('product')
                ->with(['user', 'product'])
                ->latest()
                ->get()
                ->groupBy('user_id')
                ->map(function ($userOrders) use($store){
                    $firstOrder = $userOrders->first();
                    $user = $firstOrder->user;

                    $totalSum = $userOrders->sum('total');

                    return [
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'image_url' => $user->image_url,
                        'phone' => $user->phone,
                        'total' => $totalSum,
                        'delivery_cost' => $firstOrder->delivery_cost,
                        'destination_lon' => $firstOrder->lon,
                        'destination_lat' => $firstOrder->lat,
                        'store_lon' => $store->lon,
                        'store_lat' => $store->lat,
                        'products' => $userOrders->map(function($order) {
                            return [
                                'order_id' => $order->id,
                                'created_at' => $order->created_at,
                                'product_title' => $order->product->title,
                                'product_images' => $order->product->images_urls,
                                'product_price' => $order->product->price,
                                'quantity' => $order->quantity,
                            ];
                        }),
                    ];
                })
                ->values();

            return $this->generalResponse($products_orders);
        } elseif(request('driver_id')) {
            $products_orders = ProductsOrder::whereStatus($status)
                ->whereUserId(request('user_id'))
                ->whereHas('user')
                ->whereHas('product')
                ->with(['user', 'product'])
                ->latest()
                ->get()
                ->groupBy('user_id')
                ->map(function ($userOrders) use($store){
                    $firstOrder = $userOrders->first();
                    $user = $firstOrder->user;

                    $totalSum = $userOrders->sum('total');

                    return [
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'image_url' => $user->image_url,
                        'phone' => $user->phone,
                        'total' => $totalSum,
                        'delivery_cost' => $firstOrder->delivery_cost,
                        'destination_lon' => $firstOrder->lon,
                        'destination_lat' => $firstOrder->lat,
                        'store_lon' => $store->lon,
                        'store_lat' => $store->lat,
                        'products' => $userOrders->map(function($order) {
                            return [
                                'order_id' => $order->id,
                                'created_at' => $order->created_at,
                                'product_title' => $order->product->title,
                                'product_images' => $order->product->images_urls,
                                'product_price' => $order->product->price,
                                'quantity' => $order->quantity,
                            ];
                        }),
                    ];
                })
                ->values();

            return $this->generalResponse($products_orders);
        } else {
                $products_orders = ProductsOrder::whereStatus($status)
                ->whereHas('user')
                ->whereHas('product')
                ->with(['user', 'product'])
                ->latest()
                ->get()
                ->groupBy('user_id')
                ->map(function ($userOrders) use($store){
                    $firstOrder = $userOrders->first();
                    $user = $firstOrder->user;

                    $totalSum = $userOrders->sum('total');

                    return [
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'image_url' => $user->image_url,
                        'phone' => $user->phone,
                        'total' => $totalSum,
                        'delivery_cost' => $firstOrder->delivery_cost,
                        'destination_lon' => $firstOrder->lon,
                        'destination_lat' => $firstOrder->lat,
                        'store_lon' => $store->lon,
                        'store_lat' => $store->lat,
                        'products' => $userOrders->map(function($order) {
                            return [
                                'order_id' => $order->id,
                                'created_at' => $order->created_at,
                                'product_title' => $order->product->title,
                                'product_images' => $order->product->images_urls,
                                'product_price' => $order->product->price,
                                'quantity' => $order->quantity,
                            ];
                        }),
                    ];
                })
                ->values();

            return $this->generalResponse($products_orders);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        ProductsOrder::whereUserId(request('user_id'))->whereStatus('in_delivery')->get()->map(function($order) {
            $order->forceFill([
                'status' => 'canceled',
                'employee_id' => null
            ]);
            $order->save();
        });
        return $this->generalResponse(null);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
