<?php

namespace App\Http\Requests\Orders;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;

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
            'km_distance' => ['required', 'numeric'],
            'lon' => ['required', 'string'],
            'lat' => ['required', 'string'],
        ];
    }

    public function confirm() {
        $user = $this->user();
        $delivery_cost = Store::first()->km_price * $this->km_distance;
        $user->products_orders()->whereStatus('pending')->update([
            'status' => 'confirmed',
            'lon' => $this->lon,
            'lat' => $this->lat,
            'delivery_cost' => $delivery_cost,
        ]);
        return $this->generalResponse(null);
    }
}
