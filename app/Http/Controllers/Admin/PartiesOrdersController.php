<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartiesOrder;
use App\Notifications\FcmNotification;
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
            return $this->generalResponse(PartiesOrder::latest()->whereStatus($status)->with('user')->get());
        }
        return $this->generalResponse(PartiesOrder::latest()->with('user')->get());

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
    public function update(Request $request, PartiesOrder $parties_order)
    {
        $status = $request->status;
        if($status == 'pending') {
            return $this->generalResponse(null, null, 400);
        }
        $title = 'اشعار جديد';
        if($status == 'accepted') {
            $body = 'لقد تم الموافقة على طلب الحفلة الخاص بك، الرجاء الانتظار حتى يحين الموعد';
        } elseif('rejected') {
            $body = 'للاسف، لقد تم رفض طلب الحفلة الخاص بك';
        } elseif('in_delivery') {
            $body = 'طلب الحفلة الخاص بك قيد التوصيل';
        } elseif('canceled') {
            $body = 'لقد تم الغاء طلب الحفلة الخاص بك';
        }
        $user = $parties_order->user;
        $user->notify(new FcmNotification($title, $body));
        $parties_order->forceFill(['status' => $status]);
        $parties_order->save();
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
