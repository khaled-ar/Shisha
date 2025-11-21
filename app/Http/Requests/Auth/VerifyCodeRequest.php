<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class VerifyCodeRequest extends FormRequest
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
            'phone' => ['required'],
            'code'     => ['required'],
        ];
    }

    public function verify_code() {
        $code = Cache::get($this->phone);
        if($code == $this->code) {
            Cache::forget($this->phone);
            return $this->generalResponse(null, null, 200);
        }
        return $this->generalResponse(null, 'Wrong Code', 400);
    }
}
