<?php

namespace App\Http\Requests\Admin\Employees;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

use function Symfony\Component\Clock\now;

class StoreEmployeeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'image' => ['required', 'image', 'mimes:png,jpg'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'front_id_image' => ['required', 'image', 'mimes:png,jpg'],
            'back_id_image' => ['required', 'image', 'mimes:png,jpg'],
            'section' => ['required', 'string', 'in:products,parties'],
            'password' => ['required', 'string', Password::min(8)->max(25)],
        ];
    }

    public function store() {
        return DB::transaction(function() {
            $user_data = $this->except(['front_id_image', 'back_id_image', 'section']);
            $employee_data = $this->only(['front_id_image', 'back_id_image', 'section']);
            $user = User::create(array_merge($user_data, ['lon' => 0, 'lat' => 0, 'phone_verified_at' => now()]));
            $user->forceFill(['role' => "employee-{$this->section}"])->save();
            $user->employee()->create($employee_data);
            return $this->generalResponse(null, '201', 201);
        });
    }
}
