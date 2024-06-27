<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.product_sku' => [
                'required',
                'string',
                Rule::in(['A', 'B', 'C', 'D', 'E']),
            ],
            '*.quantity' => 'required|integer|min:1',
        ];
    }
}
