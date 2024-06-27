<?php

namespace App\Http\Responses\Cart;

use App\Models\Cart;

class CreateCartResponse
{
    public static function response(Cart $cart)
    {

        return [
            'cart_id' => $cart->id,
            'created_at' => $cart->created_at,
        ];

    }
}
