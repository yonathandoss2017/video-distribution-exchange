<?php

namespace App\Providers;

use App\Models\Playlist;
use App\Models\Organization;
use App\Services\Vod\Ali\VodService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Tests\Services\Vod\FakeVodService;
use Illuminate\Support\ServiceProvider;
use App\Services\Storage\Oss\StorageService;
use Tests\Services\Storage\FakeStorageService;
use App\Services\Vod\VodService as VodServiceInterface;
use App\Services\Storage\StorageService as StorageServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer(
            ['partials/property_dropdown'],
            \App\Http\ViewComposers\NavbarComposer::class
        );
        View::composer(
            'partials/layout_cp',
            \App\Http\ViewComposers\CpHeaderComposer::class
        );
        View::composer(
            'partials/layout_sp',
            \App\Http\ViewComposers\SpHeaderComposer::class
        );
        View::composer('partials/layout_home', function ($view) {
            $orgName = Organization::Organization()->name;
            $reviewPlaylistCount = Organization::Organization()->playlists()->where('playlists.publish_status', Playlist::PUBLISH_STATUS_REVIEW)->count();
            $view->with(['orgName' => $orgName, 'reviewPlaylistCount' => $reviewPlaylistCount]);
        });
        View::composer(
            ['partials/marketplace/layout_mp', 'partials/marketplace/navbar_playlist', 'partials/marketplace/navbar_home', 'partials/marketplace/left_nav_channel'],
            \App\Http\ViewComposers\MarketplaceComposer::class
        );

        Paginator::useBootstrapThree();
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        if ($this->app->environment(['production', 'staging', 'feature'])) {
            //Only enable bugsnag in production, staging, and feature environment
            $this->app->alias('bugsnag.multi', \Psr\Log\LoggerInterface::class);
        }

        if ('testing' == app()->environment()) {
            $this->app->bind(StorageServiceInterface::class, FakeStorageService::class);
            $this->app->bind(VodServiceInterface::class, FakeVodService::class);
        } else {
            $this->app->bind(StorageServiceInterface::class, StorageService::class);
            $this->app->bind(VodServiceInterface::class, VodService::class);
        }
    }
}
