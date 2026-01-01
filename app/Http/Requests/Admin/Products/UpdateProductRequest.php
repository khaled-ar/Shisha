<?php

namespace App\Http\Requests\Admin\Products;

use App\Models\Product;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class UpdateProductRequest extends FormRequest
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
            'title' => ['string'],
            'description' => ['string'],
            'price' => ['string'],
            'quantity' => ['integer', 'min:1'],
            'images' => ['array'],
            'images.*' => ['image', 'mimes:png,jpg'],
            'category_id' => ['integer', 'exists:categories,id']
        ];
    }

    public function update($product) {
        return DB::transaction(function() use($product) {
            $product->update($this->validated());
            $user = $this->user();
            if($user->role == 'employee-products') {
                Notification::send(User::whereRole('admin')->get(),
                    new FcmNotification('اشعار جديد', "لقد قام {$user->name} بتعديل المنتج رقم {$product->id}"));
            }
            return $this->generalResponse(null, 'Updated Successfully', 200);
        });
    }
}
