<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The hotel name is required.',
            'name.string' => 'The hotel name must be a valid text string.',
            'name.max' => 'The hotel name must not exceed 255 characters.',
            'user_id.required' => 'Please assign an owner to this hotel.',
            'user_id.exists' => 'The selected owner is invalid or does not exist in the system.',
        ];
    }
}
