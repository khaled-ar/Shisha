<?php

namespace App\Http\Requests\Admin\Products;

use App\Models\Product;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class StoreProductRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:png,jpg'],
            'category_id' => ['required', 'integer', 'exists:categories,id']
        ];
    }

    public function store() {
        return DB::transaction(function() {
            Product::create($this->except('images'));
            $user = $this->user();
            if($user->role == 'employee-products') {
                Notification::send(User::whereRole('admin')->get(),
                    new FcmNotification('اشعار جديد', "لقد قام {$user->name} بإضافة منتج جديد، الرجاء الاطلاع"));
            }
            return $this->generalResponse(null, '201', 201);
        });
    }
}
