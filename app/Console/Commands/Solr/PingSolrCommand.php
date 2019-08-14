<?php

namespace App\Console\Commands\Solr;

use Exception;
use Solarium\Client;
use Illuminate\Console\Command;
use App\Services\Solr\SolrService;

class PingSolrCommand extends Command
{
    protected $signature = 'solr:ping
        {core*}';

    protected $description = 'Ping Solr';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cores = $this->argument('core');

        foreach ($cores as $core) {
            $config = SolrService::getSolrConfig($core);
            if (isset($config['error'])) {
                $this->error($config['error']);
                continue;
            }
            $client = new Client($config);
            $client->setDefaultEndpoint(Config('solarium.default'));

            // create a ping query
            $ping = $client->createPing();
            try {
                // execute the ping query
                $response = $client->ping($ping);

                $this->info('=====================================');
                $this->info('              PING SOLR              ');
                $this->info('=====================================');
                $this->info('Core name: '.strtoupper($core));
                $this->info('Headers: '.$response->getResponse()->getHeaders()[0]);
                $this->info('statusCode: '.$response->getResponse()->getStatusCode());
                $this->info('statusMessage: '.$response->getResponse()->getStatusMessage());
                $this->info('-------------------------------------');
                $this->info('Default Endpoint: '.Config('solarium.default'));
                $this->info('Core: '.$client->getEndpoint()->getCore());
                $this->info('Host: '.$client->getEndpoint()->getHost());
            } catch (Exception $e) {
                $this->error($e);
            }
        }
    }
}
