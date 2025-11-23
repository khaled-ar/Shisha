<?php

namespace App\Http\Requests\Orders;

use App\Models\Product;
use App\Models\ProductsOrder;
use Illuminate\Foundation\Http\FormRequest;

class StoreProdutsOrderRequest extends FormRequest
{

    public function __construct(private Product $product) {
        $this->product = Product::whereId(request('product_id'))->first();
    }

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
        $quantity = $this->product ? $this->product->quantity : 0;

        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'max:' . $quantity],
        ];
    }

    public function store() {
        ProductsOrder::create(array_merge($this->validated(), [
            'user_id' => $this->user()->id,
            'total' => $this->quantity * $this->product->price,
        ]));

        return $this->generalResponse(null, '201', 201);
    }
}
