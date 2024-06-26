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
     * @param CreateCartRequest $request
     * @return Cart
     */
    public static function create(CreateCartRequest $request): Cart
    {
        $cart = new Cart();
        $cart->save();

        return $cart;
    }

    /**
     * Returns the related products to this cart
     */
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartProduct::class);
    }
}
