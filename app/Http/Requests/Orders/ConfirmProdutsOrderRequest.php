<?php

namespace App\Http\Requests\Orders;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Notification;

use function Symfony\Component\Clock\now;

class ConfirmProdutsOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_cost' => ['required', 'numeric'],
            'lon' => ['required', 'string'],
            'lat' => ['required', 'string'],
        ];
    }

    public function confirm() {
        $user = $this->user();
        $user->products_orders()->whereStatus('pending')->update([
            'status' => 'confirmed',
            'lon' => $this->lon,
            'lat' => $this->lat,
            'delivery_cost' => $this->delivery_cost,
            'confirmed_at' => now(),
        ]);
        $users = [];
        $available_drivers = Employee::whereWorkStatus('active')->with('user')->get()
            ->map(function($driver) use(&$users){
                $users[] = $driver->user;
            });
        Notification::send($users,
            new FcmNotification('اشعار جديد', 'هناك طلب جديد، الرجاء الاطلاع'));
        return $this->generalResponse(null);
    }
}
