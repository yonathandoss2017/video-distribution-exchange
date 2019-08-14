<?php

namespace App\Jobs;

use App\Models\Entry;
use GuzzleHttp\Client;
use Mockery\Exception;
use Illuminate\Bus\Queueable;
use App\Models\EntryFingerprint;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VideoFingerprintExtractionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $entryIds;

    const LOG_TAG = '[Job:Video:Fingerprint Extraction]: ';
    const MAX_ATTEMPTS_TIMES = 10;

    public function __construct($entryIds)
    {
        $this->entryIds = $entryIds;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $client = new Client();
            $failed_entries = [];
            $entries = Entry::with('platformAlivod', 'fingerprint')
                ->whereDoesntHave('fingerprint', function ($q) {
                    $q->whereIn('status', [EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_PROCESSING, EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_SUCCESS]);
                })->whereIn('id', $this->entryIds)->get();
            foreach ($entries as $entry) {
                $download_url = '';
                if ($entry->hasPlatform(Entry::PLATFORM_ALIVOD)) {
                    $download_url = $entry->platformAlivod->source_url;
                }
                \Log::info(self::LOG_TAG.'video id: '.$entry->id.' is begining fingerprint extraction for video url'.$download_url.'.');
                if ($download_url) {
                    $response = $client->request('POST', config('fingerprint.create_url'), [
                        'form_params' => [
                            'video_id' => $entry->id,
                            'organization_id' => $entry->content_provider->organization_id,
                            'download_url' => $download_url,
                            'callback_url' => route('api.fingerprint.extraction.notification'),
                        ],
                    ]);
                    if (200 == $response->getStatusCode()) {
                        \Log::info(self::LOG_TAG.'video request create fingerprint response: '.$response->getBody());
                        $result = json_decode($response->getBody(), true);
                        if ('success' == $result['status']) {
                            if (!$entry->fingerprint) {
                                $entryFingerprint = EntryFingerprint::create([
                                    'entry_id' => $entry->id,
                                ]);
                            } else {
                                $entryFingerprint = $entry->fingerprint;
                            }

                            $entryFingerprint->status = EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_PROCESSING;
                            $entryFingerprint->job_id = $result['job_id'];
                            $entryFingerprint->save();
                        }
                    } else {
                        $failed_entries[] = $entry->id;
                    }
                }
            }
            if (count($failed_entries) > 0 && $this->attempts() < self::MAX_ATTEMPTS_TIMES) {
                self::dispatch($failed_entries);
            }
        } catch (Exception $e) {
            \Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
        }
    }
}
