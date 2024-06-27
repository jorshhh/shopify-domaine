<?php

namespace App\Models;

use App\CartStatus;
use App\Http\Requests\Cart\CreateCartRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['subtotal', 'tax_total', 'grand_total', 'shopify_order_id', 'status'];

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

    /**
     * Adds a product to an existing cart only if it is open
     */
    public function addProducts(array $products): void
    {

        if ($this->status == CartStatus::CLOSED) {
            throw new \ErrorException('The cart has already been closed');
        }

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

    /**
     * Adds a product to an existing cart only if it is open
     */
    public function removeProducts(array $products): void
    {

        if ($this->status == CartStatus::CLOSED) {
            throw new \ErrorException('The cart has already been closed');
        }

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
     * Updates the cart information with the shopify response
     */
    public function checkout($result)
    {
        $result['status'] = CartStatus::CLOSED;
        $this->update($result);
    }

    /**
     * Returns the related products to this cart
     */
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartProduct::class);
    }
}
