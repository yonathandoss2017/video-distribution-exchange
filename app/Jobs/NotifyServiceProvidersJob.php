<?php

namespace App\Jobs;

use App\Models\Entry;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyServiceProvidersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const LOG_TAG = '[job:NotifyServiceProvidersJ]: ';

    private $entry;

    /**
     * Create a new job instance.
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->entry->playlists as $playlist) {
            foreach ($playlist->termsOfDistributionServiceProviders() as $sp) {
                if ($sp->sp_ivst_notification_url) {
                    try {
                        Log::info(self::LOG_TAG.' POST '.$sp->sp_ivst_notification_url);
                        $client = new Client();
                        $res = $client->request('POST', $sp->sp_ivst_notification_url, [
                            'form_params' => [
                                'type' => 'video',
                                'action' => 'add',
                                'playlist_id' => $playlist->id,
                                'video_id' => $this->entry->id,
                            ],
                        ]);
                        Log::info(self::LOG_TAG.' StatusCode: '.$res->getStatusCode());
                    } catch (\Exception $exception) {
                        Log::error(self::LOG_TAG.' exception: '.$exception->getMessage());
                    }
                }
            }
        }
    }
}
