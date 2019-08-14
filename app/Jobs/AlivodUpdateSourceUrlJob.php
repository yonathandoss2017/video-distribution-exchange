<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use App\Models\PlatformAlivod;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AlivodUpdateSourceUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $callback_parameters;
    private $tag = '[job AlivodUpdateSourceUrlJob]: ';

    /**
     * Create a new job instance.
     */
    public function __construct($callback_parameters)
    {
        $this->callback_parameters = $callback_parameters;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $callback_parameters = $this->callback_parameters;
            Log::info($this->tag.'run '.$callback_parameters['EventType']);
            if ('FileUploadComplete' == $callback_parameters['EventType']) {
                $platform_alivod = PlatformAlivod::withTrashed()->where('video_id', $callback_parameters['VideoId'])->first();
                if ($platform_alivod) {
                    $platform_alivod->source_url = $callback_parameters['FileUrl'];
                    $platform_alivod->save();
                }
            }
        } catch (Exception $e) {
            Log::error($this->tag.print_r($e->getMessage(), true));
            Log::info($this->tag."\n".$e->getTraceAsString());
        }
    }
}
