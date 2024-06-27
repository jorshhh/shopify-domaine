<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;

class ShopifyService
{
    public function __construct(
        private PendingRequest $client
    ) {}

    public function getProducts(): \Illuminate\Support\Collection
    {
        $items = $this->client->get('admin/api/2024-04/products.json')->json();

        //Parse the response to something that our app can use more intuitively
        return collect($items['products'])
            ->map(function ($product) {

                $variant = $product['variants'][0];

                return [
                    'shopify_id' => $product['id'],
                    'sku' => $variant['sku'],
                    'special' => $product['body_html'],
                    'price' => $variant['price'],
                ];
            });
    }
}
