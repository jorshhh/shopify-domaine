<?php

namespace Tests\Feature\API\Cart;

use App\CartStatus;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Http\Response;
use Tests\TestCase;

class RemoveFromCartTest extends TestCase
{
    public function test_remove_from_cart(): void
    {

        $cart = Cart::factory()
            ->has(CartProduct::factory()->count(1), 'products')
            ->create();

        $product = $cart->products()->first();
        $productTotal = $product->quantity;

        $payload = [
            [
                'product_sku' => $product->product_sku,
                'quantity' => '1',
            ],
        ];

        $this->json('delete', "api/cart/$cart->id/product/remove", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'cart_id',
                    'created_at',
                ]
            );

        $product->refresh();

        $this->assertEquals($productTotal - 1, $product->quantity);

    }

    public function test_remove_from_cart_invalid_quantity(): void
    {
        $cart = Cart::factory()->create();

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '-1',
            ],
        ];

        $this->json('delete', "api/cart/$cart->id/product/remove", $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(
                [
                    'message',
                    'errors',
                ]
            );
    }

    public function test_remove_from_cart_invalid_cart(): void
    {

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '-1',
            ],
        ];

        $this->json('delete', 'api/cart/invalid/product/remove', $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_remove_from_cart_closed_cart(): void
    {

        $cart = Cart::factory()->create();
        $cart->update(['status' => CartStatus::CLOSED]);

        $payload = [
            [
                'product_sku' => 'A',
                'quantity' => '1',
            ],
        ];

        $this->json('delete', "api/cart/$cart->id/product/remove", $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
