<?php

namespace App\Http\Responses\Cart;

use App\Models\Cart;

class CheckoutCartResponse
{
    public static function response(Cart $cart): array
    {

        return [
            'cart_id' => $cart->id,
            'subtotal' => $cart->subtotal,
            'tax_total' => $cart->tax_total,
            'grand_total' => $cart->grand_total,
            'shopify_order_id' => $cart->shopify_order_id,
            'products' => $cart->products()->get(['product_sku', 'quantity']),
        ];

    }
}
