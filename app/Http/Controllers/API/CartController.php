<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Responses\Cart\CreateCartResponse;
use App\Http\Responses\Cart\GetCartResponse;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Gets the cart object
     */
    public function get(Cart $cart): \Illuminate\Http\JsonResponse
    {
        return response()
            ->json(GetCartResponse::response($cart));
    }

    /**
     * Creates a new empty cart
     */
    public function create(CreateCartRequest $request): \Illuminate\Http\JsonResponse
    {
        $cart = Cart::create($request);
        return response(201)
            ->json(CreateCartResponse::response($cart));
    }

    public function checkout(Request $request, Cart $cart) {}

    public function add(Request $request, Cart $cart) {}

    public function delete(Request $request, Cart $cart) {}
}
