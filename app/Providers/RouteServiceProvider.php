<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        //All api inside this routes don't have API Rate Limit
        $this->mapApiUnlimitedRoutes();

        //api/v1 routes
        $this->mapApiV1Routes();

        $this->mapMarketplaceTestRoutes();
        $this->mapMarketplaceRoutes();
        $this->mapManageRoutes();
        $this->mapAdminRoutes();
        $this->mapServeRoutes();
        $this->mapUiRoutes();

        //Base API and Web routes goes here and always keep it as last function.
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapDppRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "dpp" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapDppRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth', 'can:dpp-admin'],
            'namespace' => $this->namespace.'\DPP',
            'prefix' => 'dpp',
            'as' => 'dpp.',
        ], function ($router) {
            require base_path('routes/web/dpp.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    protected function mapMarketplaceRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace,
            'prefix' => 'marketplace',
            'as' => 'marketplace.',
        ], function ($router) {
            require base_path('routes/web/marketplace.php');
        });
    }

    protected function mapMarketplaceTestRoutes()
    {
        if (!\App::environment('production')) {
            Route::group([
                'middleware' => ['web'],
                'namespace' => $this->namespace,
                'prefix' => 'marketplace/test',
                'as' => 'marketplace.test.',
            ], function ($router) {
                require base_path('routes/web/marketplace_test.php');
            });
        }
    }

    protected function mapManageRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth', 'check_organization'],
            'namespace' => $this->namespace,
            'prefix' => 'manage',
        ], function ($router) {
            require base_path('routes/web/manage/organization.php');
            require base_path('routes/web/manage/cp.php');
            require base_path('routes/web/manage/sp.php');
        });
    }

    protected function mapAdminRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth', 'can:super-admin'],
            'namespace' => $this->namespace,
            'prefix' => 'admin',
            'as' => 'admin.',
        ], function ($router) {
            require base_path('routes/web/admin.php');
        });
    }

    protected function mapServeRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'prefix' => 'serve',
            'as' => 'serve.',
        ], function ($router) {
            require base_path('routes/web/serve.php');
        });
    }

    protected function mapApiV1Routes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api/v1',
            'as' => 'api.v1.',
        ], function ($router) {
            require base_path('routes/api/v1/sp-mobile.php');
        });
    }

    protected function mapApiUnlimitedRoutes()
    {
        Route::group([
            'middleware' => 'api_unlimited',
            'namespace' => $this->namespace,
            'prefix' => 'api',
            'as' => 'api.',
        ], function ($router) {
            require base_path('routes/api/unlimited.php');
        });
    }

    /**
     * These are UI routes for prototypes and demos only.
     */
    protected function mapUiRoutes()
    {
        if (!\App::environment('production')) {
            Route::group([
                'middleware' => 'web',
                'namespace' => $this->namespace,
                'prefix' => 'ui',
            ], function ($router) {
                require base_path('routes/web/ui.php');
            });
        }
    }
}
