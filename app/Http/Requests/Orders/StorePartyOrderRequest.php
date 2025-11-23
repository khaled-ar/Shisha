<?php

namespace App\Http\Requests\Orders;

use App\Models\PartiesOrder;
use App\Models\Price;
use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;

class StorePartyOrderRequest extends FormRequest
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
            'hookahs' => ['required', 'integer'],
            'hours' => ['required', 'numeric'],
            'persons' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'datetime' => ['required', 'string'],
            'lon' => ['required', 'string'],
            'lat' => ['required', 'string'],
            'km_distance' => ['required', 'integer']
        ];
    }

    public function store() {

        $delivery_cost = Store::first()->km_price * $this->km_distance;
        $prices = Price::pluck('price', 'object')->toArray();

        $order = PartiesOrder::create(array_merge($this->except('km_distance'), [
            'user_id' => $this->user()->id,
            'total' => $prices['singe_hookah'] * $this->hookahs
                + $prices['single_hour'] * $this->hours
                + $prices['single_person'] * $this->persons
                + $delivery_cost,
            ])
        );
        $order->delivery_cost = $delivery_cost;
        return $this->generalResponse($order, '201', 201);
    }
}
