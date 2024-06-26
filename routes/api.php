<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\API\CartController::class)
    ->name('cart.')
    ->prefix('cart')->group(function () {

        Route::post('create', 'create')->name('create');
        Route::get('{cart}', 'get')->name('get');
        Route::post('{cart}/checkout', 'checkout')->name('checkout');

        Route::group([
            'prefix' => '{cart}/product',
        ], function () {

            Route::put('add', 'add')->name('add');
            Route::delete('remove', 'remove')->name('remove');

        });
    });
