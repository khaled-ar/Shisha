<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\Whatsapp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'lon' => ['required', 'string', 'max:50'],
            'lat' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->max(25)],
        ];
    }

    public function store() {
        return DB::transaction(function() {
            $res = Whatsapp::send_code($this->phone);
            if($res) {
                $user = User::create($this->validated());
                return $this->generalResponse(null, 'Whatsapp Check', 201);
            }
            return $this->generalResponse(null, 'error_400', 400);
        });
    }
}
