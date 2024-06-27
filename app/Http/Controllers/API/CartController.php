<?php

namespace App\Http\Controllers\API;

use App\CartStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartRequest;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Http\Requests\Cart\RemoveCartRequest;
use App\Http\Responses\Cart\AddCartResponse;
use App\Http\Responses\Cart\CheckoutCartResponse;
use App\Http\Responses\Cart\CreateCartResponse;
use App\Http\Responses\Cart\GetCartResponse;
use App\Http\Responses\Cart\RemoveCartResponse;
use App\Models\Cart;
use App\Services\ShopifyService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected ShopifyService $shopifyClient
    ) {}

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

    public function checkout(Request $request, Cart $cart)
    {

        try {
            if ($cart->status == CartStatus::CLOSED) {
                throw new \ErrorException("This cart has already been closed and can't be checked out");
            }
            $result = $this->shopifyClient->checkout($cart);
        } catch (\ErrorException $exception) {
            return response([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ])
                ->setStatusCode(500);
        }

        $cart->checkout($result);

        return CheckoutCartResponse::response($cart);
    }

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
