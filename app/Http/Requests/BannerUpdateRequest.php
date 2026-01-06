<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
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
            'title'      => 'required|string|min:3|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'short_descp' => 'required|string|min:10|max:500',
            // 'content'    => 'required|string|min:20',
            'status'     => 'nullable|boolean',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'      => 'Blog title is required.',
            'title.min'           => 'Blog title must be at least 3 characters.',
            'image.image'         => 'Uploaded file must be an image.',
            'image.mimes'         => 'Image must be jpg, jpeg, png or webp.',
            'image.max'           => 'Image size must not exceed 2MB.',
            'short_descp.required' => 'Short description is required.',
            'short_descp.min'      => 'Short description must be at least 10 characters.',
            'content.required'    => 'Long description is required.',
            'content.min'         => 'Long description must be at least 20 characters.',
        ];
    }
}
