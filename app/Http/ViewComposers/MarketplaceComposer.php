<?php

namespace App\Http\ViewComposers;

use App;
use Illuminate\View\View;
use App\Models\PropertyCP;
use App\Services\Marketplace\CheckoutService;

class MarketplaceComposer
{
    private $app_id;
    private $api_key;
    private $index_search;
    private $requested_playlist;

    /**
     * Create a new profile composer.
     */
    public function __construct(CheckoutService $checkoutService)
    {
        $this->app_id = env('ALS_APP_ID', 'APP ID not found.');
        $this->api_key = env('ALS_FRONTEND_API_KEY', 'API KEY not found.');
        $this->index_search = 'production_'.env('ALS_INDEX_SEARCH', 'Index search not found.');
        if (!App::environment('production')) {
            $this->index_search = 'staging_'.env('ALS_INDEX_SEARCH', 'Index search not found.');
        }
        $this->requested_playlist = $checkoutService->getRequestedPlaylistCount();
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $app_id = $this->app_id;
        $api_key = $this->api_key;
        $index_search = $this->index_search;
        $requested_playlist = $this->requested_playlist;

        $cps = PropertyCP::all();
        $cps_count = count($cps);

        $view->with([
            'app_id' => $app_id,
            'api_key' => $api_key,
            'index_search' => $index_search,
            'requested_playlist' => $requested_playlist,
            'cps' => $cps,
            'cps_count' => $cps_count,
        ]);
    }
}
