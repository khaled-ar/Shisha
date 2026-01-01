<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\ProductsOrder;
use App\Notifications\FcmNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
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
        Log::info('🔔 بدء تشغيل ConfirmedOrderFlowJob - ' . now());

        // 1. معالجة الطلبات التي مضى عليها 5 دقائق أو أكثر للإلغاء
        $ordersToCancel = ProductsOrder::where('status', 'confirmed')
            ->where('confirmed_at', '<=', now()->subMinutes(5))
            ->get();

        Log::info('📊 عدد الطلبات للإلغاء (بعد 5 دقائق): ' . $ordersToCancel->count());

        foreach ($ordersToCancel as $order) {
            Log::info('❌ إلغاء الطلب #' . $order->id . ' - مضى عليه أكثر من 5 دقائق');

            // إلغاء الطلب
            $order->forceFill(['status' => 'canceled', 'confirmed_at' => null]);
            $order->save();

            // إرسال إشعار للعميل فقط
            if ($order->user) {
                $order->user->notify(
                    new FcmNotification(
                        'اشعار جديد',
                        'للاسف، لا يوجد اي سائق متاح حالياً. تم الغاء الطلب'
                    )
                );
                Log::info('📤 تم إرسال إشعار إلغاء للعميل #' . $order->user->id . ' للطلب #' . $order->id);
            } else {
                Log::warning('⚠️ لا يوجد مستخدم مرتبط بالطلب #' . $order->id);
            }
        }

        // 2. معالجة الطلبات التي مضى عليها أقل من 5 دقائق للإشعارات المتكررة
        $activeOrders = ProductsOrder::where('status', 'confirmed')
            ->where('confirmed_at', '>', now()->subMinutes(5))
            ->get();

        Log::info('📊 عدد الطلبات النشطة (أقل من 5 دقائق): ' . $activeOrders->count());

        foreach ($activeOrders as $order) {
            // حساب عدد الدقائق المنقضية منذ تأكيد الطلب
            $minutesPassed = now()->diffInMinutes($order->confirmed_at);

            Log::info('⏰ الطلب #' . $order->id . ' - مضى عليه ' . $minutesPassed . ' دقيقة');

            // طلب مؤكد منذ 0-4 دقائق (نرسل إشعار كل دقيقة)
            if ($minutesPassed < 5) {
                // الحصول على جميع السائقين المتاحين
                $activeDrivers = Employee::where('work_status', 'active')
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();

                $driversCount = $activeDrivers->count();
                Log::info('🚗 عدد السائقين المتاحين: ' . $driversCount);

                // إرسال إشعار لجميع السائقين
                if ($driversCount > 0) {
                    Notification::send(
                        $activeDrivers,
                        new FcmNotification(
                            'تذكير',
                            'هناك طلب جديد، الرجاء الاطلاع'
                        )
                    );
                    Log::info('📤 تم إرسال إشعار لـ ' . $driversCount . ' سائق للطلب #' . $order->id);
                } else {
                    Log::warning('⚠️ لا يوجد سائقين متاحين للطلب #' . $order->id);
                }
            }
        }

        // 3. تسجيل ملخص الإحصائيات
        $totalCancelled = $ordersToCancel->count();
        $totalActive = $activeOrders->count();

        Log::info('📈 ملخص التنفيذ:');
        Log::info('   - الطلبات الملغية: ' . $totalCancelled);
        Log::info('   - الطلبات النشطة: ' . $totalActive);
        Log::info('✅ انتهاء ConfirmedOrderFlowJob - ' . now());
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ فشل ConfirmedOrderFlowJob: ' . $exception->getMessage());
        Log::error('📝 Trace: ' . $exception->getTraceAsString());
    }
}
