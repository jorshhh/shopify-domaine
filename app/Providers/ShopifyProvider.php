<?php

namespace App\Providers;

use App\Services\ShopifyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ShopifyProvider extends ServiceProvider
{

    /**
     * Register our API provider.
     * For a simple project the provider and service is small
     * But for a larger one I would've used the official Shopify SDK and made a separate package
     */
    public function register(): void
    {

        $this->app->singleton(ShopifyService::class, function () {
            $client = Http::baseUrl(config('services.shopify.store_url'))
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-Shopify-Access-Token', config('services.shopify.token'));

            return new ShopifyService($client);
        });


    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
