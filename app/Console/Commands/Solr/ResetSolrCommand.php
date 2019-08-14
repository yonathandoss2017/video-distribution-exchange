<?php

namespace App\Console\Commands\Solr;

use Exception;
use Solarium\Client;
use Illuminate\Console\Command;
use App\Services\Solr\SolrService;
use Illuminate\Support\Facades\DB;

class ResetSolrCommand extends Command
{
    protected $signature = 'solr:reset {core* : The Core of Solr}';

    protected $description = 'Reset documents in Solr';

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

            $update = $client->createUpdate();

            try {
                // add the delete query and a commit command to the update query
                $update->addDeleteQuery('*:*');
                $update->addCommit();

                // this executes the query and returns the result
                $result = $client->update($update);

                // echo 'Update query executed.' . PHP_EOL;
                $this->info('=====================================');
                $this->info('              RESET CORE             ');
                $this->info('=====================================');
                $this->info('Headers: '.$result->getResponse()->getHeaders()[0]);
                $this->info('Query status: '.$result->getStatus());
                $this->info('Query time: '.$result->getQueryTime());
                $this->info('-------------------------------------');
                $this->info('Default Endpoint: '.Config('solarium.default'));
                $this->info('Core: '.$client->getEndpoint()->getCore());
                $this->info('Host: '.$client->getEndpoint()->getHost());

                if ('marketplace' == $core) {
                    DB::table('entries')->update([
                        'indexed_at_marketplace' => null,
                    ]);
                    DB::table('playlists')->update([
                        'indexed_at_marketplace' => null,
                    ]);
                }
            } catch (Exception $e) {
                $this->error($e);
            }
        }
    }
}
