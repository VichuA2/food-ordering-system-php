<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|digits:10',
            'role' => 'required|exists:roles,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The user\'s full name is required.',
            'name.string' => 'The user\'s name must be a valid text string.',
            'name.max' => 'The user\'s name must not exceed 255 characters.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please provide a valid email address format (e.g., user@example.com).',
            'email.unique' => 'This email address is already registered in the system.',
            'password.required' => 'A password is required for registering a new user.',
            'password.min' => 'The password must be at least 6 characters long for security.',
            'role.required' => 'Please assign a role to this user.',
            'role.exists' => 'The selected role is invalid or does not exist in the system.',
            'phone.digits' => 'The phone number must be exactly 10 digits long.',
        ];
    }
}
