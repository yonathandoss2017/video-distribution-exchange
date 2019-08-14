<?php

namespace App\Console\Commands\Solr;

use App\Models\Playlist;
use Illuminate\Console\Command;
use App\Services\Solr\SolrService;
use App\Jobs\Solr\SyncPlaylistToSolrJob;
use App\Jobs\Solr\SyncPlaylistEntriesToSolrMarketplace;

class SyncSolrPlaylistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solr:sync-playlist {playlist_id} {--cores=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync playlist with all entries attached to Solr';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $playlistId = $this->argument('playlist_id');
        $coresInput = $this->option('cores');
        $cores = [];

        if (!empty($coresInput)) {
            $cores = explode(',', $coresInput);
            foreach ($cores as $core) {
                if (!SolrService::isValidCore($core)) {
                    $this->error("Core $core not valid");

                    return;
                }
            }
        }

        $this->info('Sync playlist '.$playlistId.' to solr');

        $playlist = Playlist::withTrashed()->find($playlistId);

        if (!$playlist) {
            $this->info('Playlist not found');

            return;
        }

        if ($playlist->trashed()) {
            $this->info('Playlist soft deleted');
        }

        if (empty($cores) or in_array(SolrService::CORE_MARKETPLACE, $cores)) {
            $this->info('Sync to marketplace');
            SyncPlaylistToSolrJob::dispatch($playlist);
            SyncPlaylistEntriesToSolrMarketplace::dispatch($playlist);
        }
    }
}
