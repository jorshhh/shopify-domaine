<?php

namespace App\Models;

use App\Http\Requests\Cart\CreateCartRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Creates a new cart from an API request
     * We are not really using the request but on a real app we would
     */
    public static function create(CreateCartRequest $request): Cart
    {
        $cart = new Cart();
        $cart->save();

        return $cart;
    }

    public function addProducts(array $products): void
    {

        $lineItems = $this->products;

        foreach ($products as $product) {
            $lineItem = $lineItems->firstWhere('product_sku', $product['product_sku']);

            if (! $lineItem) {
                CartProduct::create($this, $product);
            } else {
                $lineItem->update([
                    'quantity' => $lineItem->quantity + $product['quantity'],
                ]);
            }
        }

    }

    public function removeProducts(array $products): void
    {

        $lineItems = $this->products;

        foreach ($products as $product) {
            $lineItem = $lineItems->firstWhere('product_sku', $product['product_sku']);
            if ($lineItem) {

                if ($lineItem->quantity - $product['quantity'] <= 0) {
                    $lineItem->delete();
                } else {
                    $lineItem->update([
                        'quantity' => $lineItem->quantity - $product['quantity'],
                    ]);
                }

            }

        }

    }

    /**
     * Returns the related products to this cart
     */
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartProduct::class);
    }
}
