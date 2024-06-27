<?php

namespace Tests\Feature\API\Cart;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateCartTest extends TestCase
{
    public function test_create_cart(): void
    {
        $this->json('post', 'api/cart/create')
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'cart_id',
                    'created_at',
                ]
            );
    }
}
