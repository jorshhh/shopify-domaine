<?php

namespace App\Providers;

use App\Services\ShopifyService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ShopifyProvider extends ServiceProvider
{

    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton(ShopifyService::class, function (Application $app) {
            $client = Http::baseUrl(config('services.shopify.store_url'))
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
