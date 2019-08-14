<?php

namespace App\Jobs\Solr;

use Carbon\Carbon;
use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Solr\SolrMarketplaceSyncService;

class AddPlaylistToSolrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_DOCS_TO_ADD = 500;

    private $playlistId;

    /**
     * Create a new job instance.
     *
     * @param $playlistId
     */
    public function __construct($playlistId)
    {
        $this->playlistId = $playlistId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        $playlists = Playlist::where('id', '>', $this->playlistId)
            ->with(['content_provider'])
            ->withCount(['entries' => function ($query) {
                $query->published()->ready();
            }])
            ->limit(self::MAX_DOCS_TO_ADD)
            ->get();

        if ($playlists->count() > 0) {
            $lastPlaylistId = $playlists->last()->id;
            (new SolrMarketplaceSyncService())->playlists($playlists);
            self::dispatch($lastPlaylistId);
            //->delay(Carbon::now()->addSeconds(3));
        }
    }
}
