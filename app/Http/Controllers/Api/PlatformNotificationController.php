<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\PropertyCP;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\PlatformOauth;
use App\Models\PlatformAlivod;
use App\Models\EntryAiReviewResult;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\Vod\Ali\ReviewVideoJob;
use App\Services\Storage\Oss\AliOSS;
use App\Jobs\AlivodUpdateSourceUrlJob;
use App\Jobs\NotifyServiceProvidersJob;
use App\Models\PlatformAlivodTranscode;
use App\Jobs\Vod\Ali\VideoBatchIngestJob;

class PlatformNotificationController extends Controller
{
    private $tag = '[platform notification - %s]: ';

    const COLUMN_TITLE = 0;
    const COLUMN_PLAYLIST = 1;
    const COLUMN_FILENAME = 2;
    const COLUMN_THUMBNAIL = 3;
    const COLUMN_DESC = 4;
    const COLUMN_TAGS = 5;
    const COLUMN_NEEDSNAPSHOT = 6;
    const VOD_IMAGE_DISKSPACE = 1048576;    //image disk space on vod default set 1M

    private function getTag()
    {
        return sprintf($this->tag, debug_backtrace()[1]['function']);
    }

    public function aliMnsEventsNotify(Request $request)
    {
        Log::info($this->getTag().'notification incoming');
        Log::info($this->getTag().'notification oss upload success');

        try {
            $message = base64_decode($request->getContent());
            Log::info($this->getTag().$message);

            $property = PropertyCP::where('api_key', $request->api_key)->first();
            if (is_null($property)) {
                Log::error($this->getTag().'Error = invalid api key.');

                return;
            }

            $oauth = PlatformOauth::where('property_id', $property->id)->first();
            if (is_null($oauth)) {
                Log::error($this->getTag().'Error = no alioss oauth was created for property:'.$property->id);

                return;
            }

            $message = json_decode($message, true);
            $events = $message['events'];
            foreach ($events as $event) {
                if ('ObjectCreated:PutObject' == $event['eventName'] || 'ObjectCreated:CompleteMultipartUpload' == $event['eventName']) {
                    $bucket_name = $event['oss']['bucket']['name'];
                    $object_name = $event['oss']['object']['key'];
                    if ($this->isCompleteFile($object_name)) {
                        //find the same name of csv file.
                        if ($this->isCsvExist($oauth, $bucket_name, $object_name)) {
                            $csv_datas = $this->parseCsvFile($oauth, $bucket_name, $this->getFilePath($object_name).'.csv');
                            if (!is_null($csv_datas)) {
                                $entries = [];
                                for ($i = 0; $i < count($csv_datas); ++$i) {
                                    $video_object = $this->getFilePath($object_name, $csv_datas[$i][self::COLUMN_FILENAME]);
                                    $entry = $this->createEntry($oauth, $bucket_name, $video_object, $csv_datas[$i]);
                                    if (!is_null($entry)) {
                                        $entries[] = $entry;
                                    }
                                }
                                if (count($entries) > 0) {
                                    $this->batchUploadVideoToVod($entries);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($this->getTag().print_r($e->getMessage(), true));
        }
    }

    private function isCompleteFile($object)
    {
        $extension = pathinfo($object, PATHINFO_EXTENSION);

        return 'complete' == strtolower($extension);
    }

    private function createEntry($oauth, $bucket, $video_object, $csv_datas)
    {
        $entry = $this->storeEntryDatas($oauth, $csv_datas);
        if (!is_null($entry)) {
            $entry->addToPlaylist($csv_datas[self::COLUMN_PLAYLIST]);
            Log::info($this->getTag().' entry create success.');

            $entry->video_url = $this->getFileUrl($oauth, $bucket, $video_object);
            if (!$csv_datas[self::COLUMN_NEEDSNAPSHOT]) {
                $entry->thumbnail_url = $this->getFileUrl($oauth, $bucket, $this->getFilePath($video_object, $csv_datas[self::COLUMN_THUMBNAIL]));
            } else {
                $entry->thumbnail_url = '';
            }

            return $entry;
        } else {
            Log::error($this->getTag().'Error = entry created failed');
        }

        return null;
    }

    private function storeEntryDatas($oauth, $csv_datas)
    {
        $entry_name = $csv_datas[self::COLUMN_TITLE];
        $entry_desc = $csv_datas[self::COLUMN_DESC];
        $metaAttributes = [];
        if (!empty($csv_datas[self::COLUMN_TAGS])) {
            $metaAttributes = [
                'tags' => $csv_datas[self::COLUMN_TAGS],
            ];
        }

        $entry = Entry::createWithPlatform(
            $oauth->property_id,
            $oauth->user_id,
            [
                'name' => $entry_name,
                'description' => $entry_desc,
                'duration' => 0,
                'published_at' => Carbon::now(),
            ],
            $metaAttributes,
            Entry::PLATFORM_ALIVOD,
            [
                'status' => PlatformAlivod::STATUS_PROCESSING,
            ]
        );

        return $entry;
    }

    /**
     * Up to 100 videos are uploaded to VOD at a time.
     *
     * @param $entries
     */
    private function batchUploadVideoToVod($entries)
    {
        $collections = collect([$entries])->collapse()->chunk(100);
        foreach ($collections as $entries_collection) {
            VideoBatchIngestJob::dispatch($entries_collection, $entries_collection->pluck('video_url')->toArray());
        }
    }

    private function getFilePath($object, $filename = '')
    {
        $dirname = pathinfo($object)['dirname'];
        if (empty($filename)) {
            $filename = pathinfo($object)['filename'];
        }
        if (!empty($dirname) && '.' != $dirname) {
            $filepath = $dirname.'/'.$filename;
        } else {
            $filepath = $filename;
        }

        return $filepath;
    }

    private function getFileUrl($oauth, $bucket, $object)
    {
        return 'https://'.$bucket.'.'.$oauth->oss_outer_endpoint.'/'.$object;
    }

    private function isVideoExist($oauth, $bucket, $object, $video_filename)
    {
        $video_object = $this->getFilePath($object, $video_filename);
        if ($this->isFileExist($oauth, $bucket, $video_object)) {
            return true;
        }

        return false;
    }

    private function isThumbnailExist($oauth, $bucket, $object, $thumbnail_filename)
    {
        $thumbnail_object = $this->getFilePath($object, $thumbnail_filename);
        if ($this->isFileExist($oauth, $bucket, $thumbnail_object)) {
            return true;
        }

        return false;
    }

    private function isCsvExist($oauth, $bucket, $object)
    {
        $csv_object = $this->getFilePath($object).'.csv';
        if ($this->isFileExist($oauth, $bucket, $csv_object)) {
            return true;
        }

        return false;
    }

    private function isFileExist($oauth, $bucket, $object)
    {
        $oss = new AliOSS($oauth->api_key, $oauth->api_secret, $oauth->oss_outer_endpoint);
        $oss->setBucket($bucket);
        if ($oss->doesObjectExist($object)) {
            return true;
        }

        return false;
    }

    private function parseCsvFile($oauth, $bucket, $csv_object)
    {
        $csv_errors = [];
        $oss = new AliOSS($oauth->api_key, $oauth->api_secret, $oauth->oss_outer_endpoint);
        $oss->setBucket($bucket);
        $csv_url = $oss->signUrl($csv_object);
        $handle = fopen($csv_url, 'r');
        if ($handle) {
            $n = 0;
            $csv_datas = [];
            while ($data = fgetcsv($handle)) {
                ++$n;
                if ($n > 1) {
                    //title,playlist,filename,thumbnail,description,tags
                    if (empty($data[self::COLUMN_TITLE])) {
                        array_push($csv_errors, 'csv文件中第'.$n.'行未添加视频的title信息');
                        break;
                    } elseif (empty($data[self::COLUMN_PLAYLIST])) {
                        array_push($csv_errors, 'csv文件中第'.$n.'行未添加视频的playlist信息');
                        break;
                    } elseif (empty($data[self::COLUMN_FILENAME])) {
                        array_push($csv_errors, 'csv文件中第'.$n.'行未添加视频的文件名信息');
                        break;
                    } else {
                        //check playlist whether exists and approve passed
                        $playlist_names = explode(',', $data[self::COLUMN_PLAYLIST]);
                        $playlists = Playlist::whereIn('name', $playlist_names)->where('property_id', $oauth->property_id)->get();
                        if ($playlists->count() != count($playlist_names)) {
                            array_push($csv_errors, 'csv文件中第'.$n.'行有未在系统中创建的playlist信息存在');
                            break;
                        } else {
                            //check playlist whether approve passed
                            $is_approved = true;
                            if (config('features.content_review')) {
                                foreach ($playlists as $playlist) {
                                    if (Playlist::STATUS_READY != $playlist->status) {
                                        $is_approved = false;
                                        array_push($csv_errors, 'csv文件中第'.$n.'行添加的'.$playlist->name.'未在系统中审核通过');
                                        break;
                                    }
                                }
                            }
                            //check the same name of video whether exists on oss
                            if ($is_approved) {
                                if (false == $this->isVideoExist($oauth, $bucket, $csv_object, $data[self::COLUMN_FILENAME])) {
                                    array_push($csv_errors, 'csv文件中第'.$n.'行添加的视频文件名在OSS上不存在同名的视频');
                                    break;
                                } else {
                                    //whether need snapshot
                                    $data[self::COLUMN_NEEDSNAPSHOT] = true;
                                    if (!empty($data[self::COLUMN_THUMBNAIL])) {
                                        if ($this->isThumbnailExist($oauth, $bucket, $csv_object, $data[self::COLUMN_THUMBNAIL])) {
                                            $data[self::COLUMN_NEEDSNAPSHOT] = false;
                                        }
                                    }
                                    $data[self::COLUMN_PLAYLIST] = $playlists->pluck('id')->toArray();
                                    array_push($csv_datas, $data);
                                }
                            }
                        }
                    }
                }
            }
            fclose($handle);
            if (count($csv_errors) < 1) {
                if (empty($csv_datas)) {
                    array_push($csv_errors, 'csv文件中未添加任何视频的title和playlist信息');
                } else {
                    return $csv_datas;
                }
            }
        } else {
            array_push($csv_errors, 'csv文件信息读取失败');
        }
        $oss->uploadContent($this->getFilePath($csv_object).'.err', implode(';', $csv_errors));

        return null;
    }

    public function aliVod(Request $request)
    {
        try {
            $callback_parameters_json = $request->getContent();
            Log::info($this->getTag().'request parameters:'.$callback_parameters_json);
            $callback_parameters = json_decode($callback_parameters_json, true);

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

                    Entry::whereHas('platformAlivod', function ($query) use ($callback_parameters) {
                        $query->where('job_id', $callback_parameters['JobId'])->withTrashed();
                    })->withTrashed()->update(['status' => Entry::STATUS_ERROR]);
                }
            } elseif ('FileUploadComplete' == $callback_parameters['EventType']) {
                /*
                 * FileUploadComplete和UploadByURLComplete的通知几乎同时到达
                 * FileUploadComplete需要在UploadByURLComplete完成对platform_alivods中video_id的更新后才能找到对应记录
                 * 所以此处延迟30s执行
                 */
                AlivodUpdateSourceUrlJob::dispatch($callback_parameters)->delay(now()->addSeconds(30));
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
                                $entry->duration = $stream_info['Duration'];
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
                        if (config('features.content_review')) {
                            $entry->status = Entry::STATUS_DRAFT;
                        } else {
                            $entry->status = Entry::STATUS_READY;
                            NotifyServiceProvidersJob::dispatch($entry);
                        }
                    } else {
                        $platform_alivod->status = PlatformAlivod::STATUS_TRANSCODE_FAILED;
                        $entry->status = Entry::STATUS_ERROR;
                    }

                    $entry->save();
                    $platform_alivod->save();

                    PlatformAlivodTranscode::insert($platform_alivod_transcodes);

                    //update video disk space to org
                    $property = optional($entry)->content_provider;
                    if ($property) {
                        Organization::where('id', $property->organization_id)->increment('storage_size_in_byte', $platform_alivod->disk_space_in_byte);
                    }
                }
            } elseif ('SnapshotComplete' == $callback_parameters['EventType']) {
                if ('success' == $callback_parameters['Status']) {
                    $entry = Entry::whereHas('platformAlivod', function ($query) use ($callback_parameters) {
                        $query->where('video_id', $callback_parameters['VideoId'])->withTrashed();
                    })->withTrashed()->first();
                    if ($entry) {
                        $entry->thumbnail_url = $callback_parameters['CoverUrl'];
                        $entry->save();
                    }
                }
            } elseif ('AIMediaAuditComplete' == $callback_parameters['EventType']) {
                $data = json_decode($callback_parameters['Data'], true);
                $entry_ai_review_result = EntryAiReviewResult::withTrashed()->where('jobid', $callback_parameters['JobId'])->first();
                if ($entry_ai_review_result) {
                    $entry_ai_review_result->ali_status = $callback_parameters['Status'];
                    $entry_ai_review_result->code = $callback_parameters['Code'];
                    $entry_ai_review_result->message = $callback_parameters['Message'];
                    $entry_ai_review_result->suggestion = $data['Suggestion'];
                    $entry_ai_review_result->abnormal_modules = $data['AbnormalModules'] ?? '';
                    $entry_ai_review_result->label = $data['Label'];

                    $entry_ai_review_result->save();

                    ReviewVideoJob::dispatch($entry_ai_review_result->id, $entry_ai_review_result->entry_id, $callback_parameters['MediaId']);
                } else {
                    Log::info($this->getTag().'could not find entry ai review result according to jobid:['.$callback_parameters['JobId'].']');
                }
            }
        } catch (Exception $e) {
            Log::error($this->getTag().print_r($e->getMessage(), true));
            Log::info($this->getTag()."\n".$e->getTraceAsString());
        }
    }
}
