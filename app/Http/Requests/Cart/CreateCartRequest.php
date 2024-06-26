<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CreateCartRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     * It is empty for now but on a real app it would have some validation.
     */
    public function rules(): array
    {
        return [

        ];
    }
}
