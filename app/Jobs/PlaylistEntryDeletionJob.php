<?php

namespace App\Jobs;

use Log;
use App\Models\Entry;
use Mockery\Exception;
use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlaylistEntryDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $playlist;

    const LOG_TAG = '[Job:playlist_Entry:Delete]: ';

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $entries = Entry::join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
                ->whereIn('entry_id', $this->playlist->entries()->pluck('entry_id'))
                ->take(50)
                ->get()
                ->groupBy('id')
                ->filter(function ($value, $key) {
                    return 1 == $value->count();
                });
            if ($entries->count() > 0) {
                foreach ($entries as $entry) {
                    $entry->first()->delete();
                }
                if ($entries->count() >= 50) {
                    return dispatch(new self($this->playlist));
                }
            }
            if (Playlist::STATUS_DELETE_PROCESSING == $this->playlist->status) {
                $this->playlist->delete();
            }
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
            $this->playlist->retry_count = $this->playlist->retry_count + 1;
            if (10 == $this->playlist->retry_count) {
                $this->playlist->status = Playlist::STATUS_DELETE_FAILED;
            } else {
                dispatch(new self($this->playlist));
            }
            $this->playlist->save();
        }
    }
}
