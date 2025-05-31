<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Discord\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $socialite = $this->app->make(\Laravel\Socialite\Contracts\Factory::class);

        // Register the Discord provider:
        $socialite->extend('discord', function ($app) use ($socialite) {
            $config = $app['config']['services.discord'];

            return $socialite->buildProvider(Provider::class, $config);
        });
    }
}
