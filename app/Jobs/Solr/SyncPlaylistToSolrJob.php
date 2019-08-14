<?php

namespace App\Jobs\Solr;

use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Solr\SolrMarketplaceSyncService;

class SyncPlaylistToSolrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Playlist
     */
    public $playlist;

    /**
     * Create a new job instance.
     *
     * @param Playlist $playlist
     */
    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        if (Playlist::STATUS_READY != $this->playlist->status && !$this->playlist->indexed_at_marketplace) {
            return;
        }

        $playlist = Playlist::where('id', $this->playlist->id)
            ->withCount(['entries' => function ($query) {
                $query->published()->ready();
            }])
            ->withTrashed()
            ->first();

        Log::info("Sync playlist to solr marketplace [playlist_id][{$playlist->id}]");

        (new SolrMarketplaceSyncService())->playlist($playlist);
    }
}
