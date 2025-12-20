<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\Whatsapp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
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
            'phone' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }

    public function authenticate()
    {
        return Auth::attempt(
            [
                'phone' => $this->phone,
                'password' => $this->password,
            ]
        );
    }

    public function check() {
        if($this->authenticate()) {
            $user = User::wherePhone($this->phone)->first();
            $user->fcm = request()->header('fcm');
            $user->save();
            $user['token'] = $user->createToken('auth_token')->plainTextToken;
            return $this->generalResponse([
                'token' => $user->token,
                'phone_verified_at' => $user->phone_verified_at,
                'role' => $user->role,
                'lon' => $user->lon,
                'lat' => $user->lat,
            ], null, 200);
        }
        return $this->generalResponse(null, 'Wrong Credentials', 401);
    }
}
