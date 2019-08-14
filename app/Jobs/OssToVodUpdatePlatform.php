<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\Entry;
use Illuminate\Bus\Queueable;
use App\Models\PlatformAlivod;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\PlatformAlivodTranscode;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OssToVodUpdatePlatform implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $callback_parameters;
    private $tag = '[job OssToVodUpdatePlatform]: ';

    const VOD_IMAGE_DISKSPACE = 1048576;    //image disk space on vod default set 1M

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
            if ('UploadByURLComplete' == $callback_parameters['EventType']) {
                $platform_query = PlatformAlivod::withTrashed()->where('job_id', $callback_parameters['JobId']);
                if ('success' == $callback_parameters['Status']) {
                    $platform_query->update([
                        'video_id' => $callback_parameters['VideoId'],
                        'file_size_in_byte' => $callback_parameters['Size'],
                        'disk_space_in_byte' => $callback_parameters['Size'] + self::VOD_IMAGE_DISKSPACE,
                        'status' => PlatformAlivod::STATUS_UPLOAD_COMPLETE,
                    ]);
                } else {
                    $platform_query->update([
                        'status' => PlatformAlivod::STATUS_UPLOAD_FAILED,
                    ]);
                }
            } elseif ('FileUploadComplete' == $callback_parameters['EventType']) {
                $platform_alivod = PlatformAlivod::withTrashed()->where('video_id', $callback_parameters['VideoId'])->first();
                if ($platform_alivod) {
                    $platform_alivod->source_url = $callback_parameters['FileUrl'];
                    $platform_alivod->save();
                }
            } elseif ('TranscodeComplete' == $callback_parameters['EventType']) {
                $platform_alivod = PlatformAlivod::with(['entry' => function ($query) {
                    $query->withTrashed();
                }])->withTrashed()->where('video_id', $callback_parameters['VideoId'])->first();
                if ($platform_alivod) {
                    $entry = $platform_alivod->entry;
                    $transcode_status = 0;
                    $platform_alivod_transcodes = [];
                    $datetime = Carbon::now();
                    if ('success' == $callback_parameters['Status']) {
                        foreach ($callback_parameters['StreamInfos'] as $stream_info) {
                            if ('success' == $stream_info['Status']) {
                                $transcode_status = 1;
                            }
                            $platform_alivod_transcodes[] = [
                                'platform_alivod_id' => $platform_alivod->id,
                                'size' => $stream_info['Size'],
                                'definition' => $stream_info['Definition'],
                                'fps' => $stream_info['Fps'] ?? 0,
                                'duration' => $stream_info['Duration'],
                                'bitrate' => $stream_info['Bitrate'] ?? 0,
                                'format' => $stream_info['Format'],
                                'height' => $stream_info['Height'],
                                'width' => $stream_info['Width'],
                                'status' => $stream_info['Status'],
                                'created_at' => $datetime,
                                'updated_at' => $datetime,
                            ];
                            $platform_alivod->disk_space_in_byte += $stream_info['Size'];
                        }
                    }

                    if ($transcode_status) {
                        $platform_alivod->status = PlatformAlivod::STATUS_TRANSCODE_COMPLETE;
                        $entry->platforms = Entry::PLATFORM_ALIVOD;
                        $entry->save();
                    } else {
                        $platform_alivod->status = PlatformAlivod::STATUS_TRANSCODE_FAILED;
                    }

                    $platform_alivod->save();

                    PlatformAlivodTranscode::insert($platform_alivod_transcodes);
                }
            }
        } catch (Exception $e) {
            Log::error($this->tag.print_r($e->getMessage(), true));
            Log::info($this->tag."\n".$e->getTraceAsString());
        }
    }
}
