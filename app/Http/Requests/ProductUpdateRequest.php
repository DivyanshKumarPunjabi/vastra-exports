<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'       => 'required|string|min:3|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'required|string',
            'gsm'         => 'nullable|integer|min:0',
            'moq'         => 'nullable|integer|min:1',

            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_1' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'       => 'Product title is required.',
            'category_id.required' => 'Please select a product category.',
            'category_id.exists'   => 'Selected category is invalid.',
            'moq.min'              => 'MOQ must be at least 1.',
            'gsm.min'              => 'GSM cannot be negative.',
        ];
    }
}
