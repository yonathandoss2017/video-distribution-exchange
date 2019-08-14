<?php

namespace App\Console\Commands\Solr;

use Illuminate\Console\Command;
use App\Services\Solr\SolrService;
use App\Jobs\Solr\SyncEntryToSolrJob;

class SyncSolrCommand extends Command
{
    protected $signature = 'solr:sync {--core= : The Core of Solr} {--entry_id= : The entry id(s) send to Solr}';

    protected $description = 'Sync documents to Solr';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $entryIds = $this->option('entry_id');
        if (empty($entryIds)) {
            return $this->error('entry_id is required!');
        }
        $entryIds = array_unique(explode(',', $entryIds));

        // check core
        $cores = $this->option('core');
        if (empty($cores)) {
            $cores = null;
        } else {
            $cores = array_unique(explode(',', $cores));
        }
        $cores = SolrService::getValidCores($cores);

        if (count($entryIds) > 0) {
            foreach ($entryIds as $entryId) {
                SyncEntryToSolrJob::dispatch($entryId, $cores);
            }
        }
    }
}
