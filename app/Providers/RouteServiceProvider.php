<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const Admin = '/admin';

    public const Home = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::middleware('admin')
            ->group(base_path('routes/admin.php'));

    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Storefront catalogue browsing / search-as-you-type.
        RateLimiter::for('catalog', fn (Request $request) => Limit::perMinute(240)->by('catalog|'.$request->ip()));

        // Storefront checkout.
        RateLimiter::for('orders', fn (Request $request) => Limit::perMinute(10)->by('orders|'.$request->ip()));

        // Customer sign-in / sign-up: per phone number (slows password guessing) and per IP.
        RateLimiter::for('customer-auth', fn (Request $request) => [
            Limit::perMinute(6)->by('auth-phone|'.preg_replace('/\D/', '', (string) $request->input('phone'))),
            Limit::perMinute(20)->by('auth-ip|'.$request->ip()),
        ]);
    }
}
