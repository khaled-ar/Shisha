<?php

namespace App\Http\Requests\Orders;

use App\Models\{
    PartiesOrder,
    Price,
    Store
};
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
            'persons' => ['integer'],
            'description' => ['nullable', 'string'],
            'datetime' => ['required', 'string'],
            'lon' => ['required', 'string'],
            'lat' => ['required', 'string'],
            'delivery_cost' => ['required', 'numeric']
        ];
    }

    public function store() {

        $prices = Price::pluck('price', 'object')->toArray();

        $order = PartiesOrder::create(array_merge($this->validated(), [
            'user_id' => $this->user()->id,
            'total' => $prices['single_hookah'] * $this->hookahs
                + $prices['single_hour'] * $this->hours
                + $prices['single_person'] * $this->persons
                + $this->delivery_cost
            ])
        );
        return $this->generalResponse(null, '201', 201);
    }
}
