<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
            isset($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] == 'https'
        ){
            \URL::forceScheme('https');
        }
        \Schema::defaultStringLength(191);
    }
}
