<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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
            'lon' => ['string'],
            'lat' => ['string'],
            'km_price' => ['integer'],
        ];
    }

    public function update($store) {
        $store->update($this->validated());
        return $this->generalResponse(null, 'Updated Successfully', 200);
    }
}
