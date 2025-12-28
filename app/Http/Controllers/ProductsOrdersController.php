<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\{
    ConfirmProdutsOrderRequest,
    StoreProdutsOrderRequest,
    UpdateProdutsOrderRequest,
};
use App\Models\ProductsOrder;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $store = Store::first();
        $status = request('status') ?? "pending";
        $orders = $user->products_orders()->whereStatus($status)->with('product')->get();
        $first = $status == 'pending' ? null : ($orders[0] ?? null);
        $orders->makeHidden(['lon', 'lat', 'delivery_cost']);
        return $this->generalResponse([
            'km_price' => Store::first()->km_price,
            'total' => $orders->sum('total'),
            'lon' => $first?->lon,
            'lat' => $first?->lat,
            'delivery_cost' => $first?->delivery_cost,
            'user_lon' => $user->lon,
            'user_lat' => $user->lat,
            'store_lon' => $store->lon,
            'store_lat' => $store->lat,
            'orders' => $orders
        ]);
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
    public function update(Request $request, ProductsOrder $products_order)
    {
        if($request->has('re_order') && $request->re_order == 1) {
            $products_order->forceFill(['status' => 'pending', 'confirmed_at' => null]);
            $products_order->save();
            return $this->generalResponse(null);
        }

        if($products_order->status == 'pending') {
            if($request->quantity) {
                $quantity = $request->quantity;
                $product = $products_order->product;
                $available = $product->quantity + $products_order->quantity;
                if($quantity - $products_order->quantity > $product->quantity) {
                    return response()->json([
                        'message' => "لا يمكن ان تكون كمية المنتج اكبر من {$available}",
                        'data' => null
                    ], 400);
                }
                return DB::transaction(function() use($quantity, $products_order, $product){
                    if($quantity > $products_order->quantity) {
                        $product->decrement('quantity', $quantity - $products_order->quantity);
                    } elseif($quantity < $products_order->quantity) {
                        $product->increment('quantity', $products_order->quantity - $quantity);
                    }
                    $products_order->update(['quantity' => $quantity, 'total' => $product->price * $quantity]);
                    return $this->generalResponse(null, 'Updated Successfully');
                });
            }
            return DB::transaction(function() use($products_order) {
                $products_order->forceFill(['status' => 'canceled']);
                $products_order->save();
                $products_order->product->increment('quantity', $products_order->quantity);
                return $this->generalResponse(null);
            });
        }
        return $this->generalResponse(null, 'The order cannot be updated as it is in delivery.', 400);
    }

    public function confirm(ConfirmProdutsOrderRequest $request, ProductsOrder $products_order)
    {
        return $request->confirm();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Driver Functions
    public function get_available_for_driver() {
        $driver = request()->user()->employee;
        if($driver->work_status != 'active') {
            return $this->generalResponse([]);
        }
        $store = Store::first();
        $products_orders = ProductsOrder::whereStatus('confirmed')
            ->with('user')
            ->whereHas('user')
            ->whereHas('product')
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
                ];
            })
            ->values();

        return $this->generalResponse($products_orders);
    }

    public function get_user_orders_details() {
        $user_id = request('user_id');
        $store = Store::first();
        $products_orders = ProductsOrder::whereStatus('confirmed')
            ->whereUserId($user_id)
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

    public function approve_user_orders() {
        $user_id = request('user_id');
        $products_orders = ProductsOrder::whereStatus('confirmed')
            ->whereUserId($user_id)
            ->update(['employee_id' => request()->user()->employee->id, 'status' => 'in_delivery']);
        return $this->generalResponse(null);
    }

    public function mark_orders_as_delivered() {
        $user_id = request('user_id');
        $products_orders = ProductsOrder::whereStatus('in_delivery')
            ->whereUserId($user_id)
            ->update(['status' => 'delivered']);
        return $this->generalResponse(null);
    }
}
