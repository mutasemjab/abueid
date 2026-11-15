<?php

namespace App\Providers;

use App\Services\SiteSettings;
use App\Services\Storefront;
use App\Services\StorefrontPayload;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);
        $this->app->singleton(Storefront::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(['layouts.front', 'front.*'], function (ViewContract $view) {
            $view->with([
                'settings' => $this->app->make(SiteSettings::class),
                'storefront' => $this->app->make(Storefront::class),
            ]);
        });

        View::composer('layouts.front', function (ViewContract $view) {
            $view->with('payload', $this->app->make(StorefrontPayload::class)->build());
        });
    }
}
