<?php

namespace Tests\Feature\API\Cart;

use App\CartStatus;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckoutCartTest extends TestCase
{
    /**
     * We are ignoring this one because I would have to mock the shopify api
     */

    //        public function test_checkout_cart(): void
    //        {
    //            $cart = Cart::factory()
    //                ->has(CartProduct::factory()->count(1), 'products')
    //                ->create();
    //
    //            $this->json('post', "api/cart/$cart->id/checkout")
    //                ->assertStatus(Response::HTTP_OK);
    //        }

    public function test_checkout_cart_invalid_cart(): void
    {
        $this->json('post', 'api/cart/invalid/checkout')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_checkout_cart_closed_cart(): void
    {
        $cart = Cart::factory()
            ->has(CartProduct::factory()->count(1), 'products')
            ->create();
        $cart->update(['status' => CartStatus::CLOSED]);

        $this->json('post', "api/cart/$cart->id/checkout")
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
