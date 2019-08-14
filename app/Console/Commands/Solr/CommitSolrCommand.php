<?php

namespace App\Console\Commands\Solr;

use Exception;
use Solarium\Client;
use Illuminate\Console\Command;
use App\Services\Solr\SolrService;

class CommitSolrCommand extends Command
{
    protected $signature = 'solr:commit
        {core* : The Core of Solr}';

    protected $description = 'Commit documents of Solr';

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

            try {
                $update = $client->createUpdate();
                $update->addCommit();

                // this executes the query and returns the result
                $result = $client->update($update);

                $this->info('=====================================');
                $this->info('              COMMIT SOLR            ');
                $this->info('=====================================');
                $this->info('Core name: '.strtoupper($core));
                $this->info('Update query executed.');
                $this->info('Query status: '.$result->getStatus());
                $this->info('Query time: '.$result->getQueryTime());
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
