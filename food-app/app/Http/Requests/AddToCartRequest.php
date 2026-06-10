<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Please select a food item to add to your cart.',
            'product_id.exists' => 'The selected food item does not exist. Please choose a valid item from the menu.',
            'quantity.required' => 'Please specify the quantity of the food item you want to add.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'You must add at least one item to your cart.'
        ];
    }
}
