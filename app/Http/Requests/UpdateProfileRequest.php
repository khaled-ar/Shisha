<?php

namespace App\Http\Requests;

use App\Traits\Files;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
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
            'lon' => ['string', 'max:50'],
            'lat' => ['string', 'max:50'],
            'image' => ['image', 'mimes:png,jpg'],
            'password' => ['string', Password::min(8)->max(25)],
        ];
    }

    public function update() {
        return DB::transaction(function() {
            $user = $this->user();
            $data = $this->except(['password', 'image']);
            if($user->role == 'employee-driver') {
                $data = $this->only(['password', 'image']);
            }
            if($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            if(request()->hasFile('image')) {
                Files::deleteFile(public_path("Images/Users/{$user->image}"));
                $data['image'] = Files::moveFile(request()->file('image'), 'Images/Users');
            }
            $user->update($data);
            if($user->role == 'employee-driver') {
                unset($user->phone_verified_at, $user->lon, $user->lat, $user->role);
            }
            return $this->generalResponse($user, 'Updated Successfully', 200);
        });
    }
}
