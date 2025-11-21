<?php

namespace App\Http\Requests\Admin\Employees;

use App\Traits\Files;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeRequest extends FormRequest
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
            'name' => ['string', 'max:100'],
            'image' => ['image', 'mimes:png,jpg'],
            'phone' => ['string', 'unique:users,phone'],
            'front_id_image' => ['image', 'mimes:png,jpg'],
            'back_id_image' => ['image', 'mimes:png,jpg'],
            'section' => ['string', 'in:products,parties,driver'],
        ];
    }

    public function update($employee) {
        return DB::transaction(function() use($employee) {
            $user_data = $this->only(['phone', 'name']);
            $employee_data = $this->only(['section']);
            $employee->user()->update($user_data);
            $employee->update($employee_data);

            if($this->section) {
                $employee->user->role = "employee-{$this->section}";
                $employee->user->save();
            }

            if($this->hasFile('image')) {
                Files::deleteFile(public_path("Images/Users/{$employee->user->image}"));
                $image = Files::moveFile($this->image, 'Images/Users');
                $employee->user()->update(['image' => $image]);
            }

            if($this->hasFile('front_id_image')) {
                Files::deleteFile(public_path("Images/Users/{$employee->front_id_image}"));
                $image = Files::moveFile($this->front_id_image, 'Images/Users');
                $employee->update(['front_id_image' => $image]);
            }

            if($this->hasFile('back_id_image')) {
                Files::deleteFile(public_path("Images/Users/{$employee->back_id_image}"));
                $image = Files::moveFile($this->back_id_image, 'Images/Users');
                $employee->update(['back_id_image' => $image]);
            }

            return $this->generalResponse(null, null, 200);
        });
    }
}
