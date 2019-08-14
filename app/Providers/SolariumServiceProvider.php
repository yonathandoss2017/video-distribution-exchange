<?php

namespace App\Providers;

use Solarium\Client;
use App\Services\Solr\SolrService;
use Illuminate\Support\ServiceProvider;

class SolariumServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind(Client::class, function ($app) {
            $config = SolrService::getSolrConfig('marketplace');
            $client = new Client($config);
            $client->setDefaultEndpoint(config('solarium.default'));

            return $client;
        });
    }

    public function provides()
    {
        return [Client::class];
    }
}
