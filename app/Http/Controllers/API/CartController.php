<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartRequest;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Requests\Cart\RemoveCartRequest;
use App\Http\Responses\Cart\AddCartResponse;
use App\Http\Responses\Cart\CreateCartResponse;
use App\Http\Responses\Cart\GetCartResponse;
use App\Http\Responses\Cart\RemoveCartResponse;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Gets the cart object
     */
    public function get(Cart $cart)
    {
        return response(GetCartResponse::response($cart));
    }

    /**
     * Creates a new empty cart
     */
    public function create(CreateCartRequest $request)
    {
        $cart = Cart::create($request);

        return response(CreateCartResponse::response($cart))
            ->setStatusCode(201);
    }

    public function checkout(Request $request, Cart $cart) {}

    public function add(AddCartRequest $request, Cart $cart)
    {
        $cart->addProducts($request->all());

        return response(AddCartResponse::response($cart));
    }

    public function remove(RemoveCartRequest $request, Cart $cart)
    {
        $cart->removeProducts($request->all());

        return response(RemoveCartResponse::response($cart));
    }
}
