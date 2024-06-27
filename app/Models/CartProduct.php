<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sku', 'quantity', 'cart_id', 'special', 'shopify_info',
    ];

    public static function create(Cart $cart, array $product)
    {
        $cartProduct = new CartProduct();
        $cartProduct->cart_id = $cart->id;
        $cartProduct->product_sku = $product['product_sku'];
        $cartProduct->quantity = $product['quantity'];
        $cartProduct->save();

        return $cartProduct;
    }
}
