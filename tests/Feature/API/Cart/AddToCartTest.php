<?php

namespace Tests\Feature\API\Cart;

use App\CartStatus;
use App\Models\Cart;
use Illuminate\Http\Response;
use Tests\TestCase;

class AddToCartTest extends TestCase
{
    public function test_add_to_cart(): void
    {

        $cart = Cart::factory()->create();

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '1',
            ],
        ];

        $this->json('put', "api/cart/$cart->id/product/add", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'cart_id',
                    'created_at',
                ]
            );
        $this->assertEquals(count($payload), $cart->products()->count());

    }

    public function test_add_to_cart_invalid_quantity(): void
    {
        $cart = Cart::factory()->create();

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '-1',
            ],
        ];

        $this->json('put', "api/cart/$cart->id/product/add", $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(
                [
                    'message',
                    'errors',
                ]
            );
    }

    public function test_add_to_cart_invalid_cart(): void
    {

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '-1',
            ],
        ];

        $this->json('put', 'api/cart/invalid/product/add', $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_add_to_cart_closed_cart(): void
    {

        $cart = Cart::factory()->create();
        $cart->update(['status' => CartStatus::CLOSED]);

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '1',
            ],
        ];

        $this->json('put', "api/cart/$cart->id/product/add", $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
