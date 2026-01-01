<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\ProductsOrder;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class ConfirmedOrderFlowJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. معالجة الطلبات التي مضى عليها 5 دقائق أو أكثر للإلغاء
        $ordersToCancel = ProductsOrder::where('status', 'confirmed')
            ->where('confirmed_at', '<=', now()->subMinutes(5))
            ->get();

        foreach ($ordersToCancel as $order) {
            // إلغاء الطلب
            $order->update(['status' => 'canceled']);

            // إرسال إشعار للعميل فقط
            if ($order->user) {
                $order->user->notify(
                    new FcmNotification(
                        'اشعار جديد',
                        'للاسف، لا يوجد اي سائق متاح حالياً. تم الغاء الطلب'
                    )
                );
            }
        }

        // 2. معالجة الطلبات التي مضى عليها أقل من 5 دقائق للإشعارات المتكررة
        $activeOrders = ProductsOrder::where('status', 'confirmed')
            ->where('confirmed_at', '>', now()->subMinutes(5))
            ->get();

        foreach ($activeOrders as $order) {
            // حساب عدد الدقائق المنقضية منذ تأكيد الطلب
            $minutesPassed = now()->diffInMinutes($order->confirmed_at);

            // طلب مؤكد منذ 0-4 دقائق (نرسل إشعار كل دقيقة)
            // الإشعارات ترسل في الدقائق: 0, 1, 2, 3, 4 (5 إشعارات)
            if ($minutesPassed < 5) {
                // الحصول على جميع السائقين المتاحين
                $activeDrivers = Employee::where('work_status', 'active')
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();

                // إرسال إشعار لجميع السائقين
                if ($activeDrivers->isNotEmpty()) {
                    Notification::send(
                        $activeDrivers,
                        new FcmNotification(
                            'اشعار جديد',
                            'هناك طلب جديد، الرجاء الاطلاع'
                        )
                    );
                }
            }
        }
    }
}
