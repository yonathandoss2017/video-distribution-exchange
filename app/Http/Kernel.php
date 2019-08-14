<?php

namespace App\Http;

use App\Http\Middleware\LoginAsAdmin;
use App\Http\Middleware\Api\IsContentProvider;
use App\Http\Middleware\Api\IsServiceProvider;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\UpdateUserLastActive::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            \Barryvdh\Cors\HandleCors::class,
        ],

        'api_unlimited' => [
            'bindings',
            \Barryvdh\Cors\HandleCors::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'Fideloper\Proxy\TrustProxies',
        'cors' => \App\Http\Middleware\Cors::class,
        'check_superadmin' => \App\Http\Middleware\CheckSuperadmin::class,
        'check_organization' => \App\Http\Middleware\CheckOrganization::class,
        'check_cp' => \App\Http\Middleware\CheckContentProvider::class,
        'check_sp' => \App\Http\Middleware\CheckServiceProvider::class,
        'is_content_provider' => IsContentProvider::class,
        'is_service_provider' => IsServiceProvider::class,
        'login_admin' => LoginAsAdmin::class,
    ];
}
