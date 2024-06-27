<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;

class ShopifyService
{
    public function __construct(
        private PendingRequest $client
    ) {}

    /**
     * Fetches all products from shopify
     */
    public function getProducts(): \Illuminate\Support\Collection
    {

        $response = $this->client->get('admin/api/2024-04/products.json');

        if ($response->status() !== 200) {
            throw new \ErrorException("Shopify API returned error status code {$response->status()} for the products");
        }

        $items = $response->json();

        //Parse the response to something that our app can use more intuitively
        return collect($items['products'])
            ->map(function ($product) {

                $variant = $product['variants'][0];

                return [
                    'shopify_id' => $product['id'],
                    'sku' => $variant['sku'],
                    'variant_id' => $variant['id'],
                    'special' => $product['body_html'],
                    'price' => $variant['price'],
                ];
            });
    }

    /**
     *
     * @throws ConnectionException
     * @throws \ErrorException
     */
    public function checkout(Cart $cart)
    {

        //We get a list of all the products
        $products = $this->getProducts();


        //Get the line items ready for the order taking into account special pricing
        $line_items = $this->processVolumeRules($products, $cart);

        $tax = 0;
        foreach ($line_items as $line_item) {
            $tax += ($line_item['price'] * $line_item['quantity']) / 10;
        }

        //Now we create an order and close it
        $orderData = [
            'order' => [
                'line_items' => $line_items,
                'tax_lines' => [[
                    'price' => $tax,
                    'rate' => 0.1,
                    'title' => 'Sales tax',
                ]],
                'billing_address' => [
                    'first_name' => 'Jorge',
                    'last_name' => 'Rangel',
                    'address1' => '123 Fake St',
                    'phone' => '555-555-5555',
                    'city' => 'Toronto',
                    'province' => 'ON',
                    'country' => 'CA',
                    'zip' => 'M5V 0J2',
                ],
                'shipping_address' => [
                    'first_name' => 'Jorge',
                    'last_name' => 'Rangel',
                    'address1' => '123 Fake St',
                    'phone' => '555-555-5555',
                    'city' => 'Toronto',
                    'province' => 'ON',
                    'country' => 'CA',
                    'zip' => 'M5V 0J2',
                ],
                'financial_status' => 'paid',
            ]];

        $response = $this->client->post('admin/api/2024-04/orders.json', $orderData);
        if ($response->status() !== 201) {
            throw new \ErrorException('There was an error creating the shopify order');
        }

        $responseOrderObject = $response->object()->order;

        //We take the response and simplify it so it can be saved locally
        //This data will be used to populate our model. On a larger project this could be a more strict class
        return [
            'subtotal' => $responseOrderObject->current_subtotal_price,
            'tax_total' => $responseOrderObject->current_total_tax,
            'grand_total' => $responseOrderObject->current_total_price,
            'shopify_order_id' => $responseOrderObject->id,
        ];
    }

    //This function adjusts the pricing and number of items for checkout on the database
    private function processVolumeRules($products, $cart): \Illuminate\Support\Collection
    {

        $lineItems = collect();

        foreach ($cart->products as $lineItem) {

            $shopifyProduct = $products->firstWhere('sku', $lineItem->product_sku);

            switch ($lineItem->product_sku) {

                case 'A':

                    $regularPrice = $shopifyProduct['price'];
                    $regularSales = $lineItem->quantity % 5;

                    if ($lineItem->quantity - $regularSales > 0) {
                        $lineItems->push([
                            'title' => 'Product A, volume pricing',
                            'variant_id' => $shopifyProduct['variant_id'],
                            'quantity' => $lineItem->quantity - $regularSales,
                            'price' => 1.8,
                            'fulfillment_status' => 'fulfilled',
                        ]);
                    }

                    if ($regularSales > 0) {

                        $lineItems->push([
                            'title' => 'Product A',
                            'variant_id' => $shopifyProduct['variant_id'],
                            'quantity' => $regularSales,
                            'price' => $regularPrice,
                            'fulfillment_status' => 'fulfilled',
                        ]);
                    }

                    break;

                case 'E':

                    $freeWithB = $cart->products->firstWhere('product_sku', 'B')->quantity;
                    $productsInCart = $lineItem->quantity;

                    $lineItems->push([
                        'title' => 'Product E, complimentary with product B',
                        'variant_id' => $products->firstWhere('sku', 'E')['variant_id'],
                        'quantity' => $freeWithB,
                        'price' => 0,
                        'fulfillment_status' => 'fulfilled',
                    ]);

                    if ($productsInCart > $freeWithB) {
                        $lineItems->push([
                            'variant_id' => $shopifyProduct['variant_id'],
                            'quantity' => $lineItem->quantity - $freeWithB,
                            'price' => $shopifyProduct['price'],
                            'fulfillment_status' => 'fulfilled',
                        ]);
                    }

                    break;
                case 'C':

                    $regularPrice = $shopifyProduct['price'];
                    $regularSales = $lineItem->quantity % 6;

                    if ($lineItem->quantity - $regularSales > 0) {

                        $lineItems->push([
                            'title' => 'Product C, volume pricing',
                            'variant_id' => $shopifyProduct['variant_id'],
                            'quantity' => $lineItem->quantity - $regularSales,
                            'price' => 1,
                            'fulfillment_status' => 'fulfilled',
                        ]);
                    }

                    if ($regularSales > 0) {
                        $lineItems->push([
                            'title' => 'Product C',
                            'variant_id' => $shopifyProduct['variant_id'],
                            'quantity' => $regularSales,
                            'price' => $regularPrice,
                            'fulfillment_status' => 'fulfilled',
                        ]);
                    }
                    break;
                default:
                    $lineItems->push([
                        'variant_id' => $shopifyProduct['variant_id'],
                        'quantity' => $lineItem->quantity,
                        'price' => $shopifyProduct['price'],
                        'fulfillment_status' => 'fulfilled',
                    ]);
                    break;
            }

        }

        return $lineItems;
    }
}
