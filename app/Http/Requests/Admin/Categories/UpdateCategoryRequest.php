<?php

namespace App\Http\Requests\Admin\Categories;

use App\Traits\Files;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['string', 'unique:categories,name'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'logo' => ['image', 'mimes:png,jpg']
        ];
    }

    public function update($category) {
        $category->update($this->except('logo'));
        if($this->hasFile('logo')) {
            Files::deleteFile(public_path("Images/Logos/{$category->logo}"));
            $logo = Files::moveFile($this->logo, 'Images/Logos');
            $category->update(['logo' => $logo]);
        }
        return $this->generalResponse(null, 'Updated Successfully', 200);
    }
}
