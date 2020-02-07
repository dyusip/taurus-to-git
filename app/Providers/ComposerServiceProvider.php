<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        View::composer('Management.header','App\Http\ViewComposers\NotificationComposer');
        View::composer('Management.sidebar','App\Http\ViewComposers\NotificationComposer');
        View::composer('Partsman.header','App\Http\ViewComposers\NotificationComposer');
        View::composer('Purchasing.header','App\Http\ViewComposers\NotificationComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
