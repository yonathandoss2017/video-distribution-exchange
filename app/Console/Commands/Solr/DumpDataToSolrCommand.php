<?php

namespace App\Console\Commands\Solr;

use Illuminate\Console\Command;
use App\Services\Solr\SolrService;
use App\Jobs\Solr\AddEntriesToSolrJob;
use App\Jobs\Solr\AddPlaylistToSolrJob;

class DumpDataToSolrCommand extends Command
{
    protected $signature = 'solr:dump {core* : The Core of Solr}';

    protected $description = 'Dump data from database then send it to Solr';

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

            echo PHP_EOL;
            echo '=================================================='.PHP_EOL;
            echo '              DUMP DATA TO SOLR'.PHP_EOL;
            echo '=================================================='.PHP_EOL;

            $solrCore = null;
            if ('marketplace' == $core) {
                AddEntriesToSolrJob::dispatch(0, SolrService::CORE_MARKETPLACE);
                AddPlaylistToSolrJob::dispatch(0);
            }

            echo PHP_EOL;
            echo '=================================================='.PHP_EOL;
            echo '=================================================='.PHP_EOL;
        }
    }
}
