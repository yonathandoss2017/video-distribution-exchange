<?php

namespace App\Jobs;

use Log;
use App\Models\Entry;
use Mockery\Exception;
use Illuminate\Bus\Queueable;
use App\Models\EntryAnzhengEvidence;
use Illuminate\Queue\SerializesModels;
use App\Models\PlaylistEvidenceRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Blockchain\AnZhengEvidenceService;

class AnzhengSyncFileInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    private $entry_ids;
    private $entity;

    const LOG_TAG = '[sync file info] ';

    /**
     * Create a new job instance.
     */
    public function __construct($entry_ids, $entity)
    {
        $this->entry_ids = $entry_ids;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     */
    public function handle(AnZhengEvidenceService $anZhengEvidenceService)
    {
        $filelist = [];
        $microtime = intval(ceil(microtime(true) * 1000));

        $evidenced_entry_ids = [];
        $error_evidenced_entry_ids = [];

        try {
            $entries = Entry::whereIn('id', $this->entry_ids)->with('anzhengEvidence')->get();
            foreach ($entries as $entry) {
                if ($entry->anzhengEvidence) {
                    if ($entry->anzhengEvidence->status > EntryAnzhengEvidence::STATUS_REQUEST_EVIDENCE && $entry->anzhengEvidence->status < EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR) {
                        continue;
                    }
                }
                if (Entry::PLATFORM_ALIVOD == $entry->platforms) {
                    if ($entry->platformAlivod->source_url) {
                        if (!$this->url_exists($entry->platformAlivod->source_url)) {
                            Log::error(self::LOG_TAG.'source url of entry['.$entry->id.'] is wrong');

                            $error_evidenced_entry_ids[] = $entry->id;

                            continue;
                        }

                        $evidenced_entry_ids[] = $entry->id;
                        $filelist[] = [
                            'fileName' => basename($entry->platformAlivod->source_url),
                            'BfileName' => $entry->name,
                            'noperTime' => 0,
                            'evTime' => $microtime,
                            'fileSize' => intval($entry->platformAlivod->file_size_in_byte),
                            'sha256' => hash_file('sha256', $entry->platformAlivod->source_url),
                            //'sha256' => hash_file('sha256', config('oss.oss_endpoint_protocol').config('oss.bucket').'.'.config('oss.endpoint').'/'.$entry->platformAlivod->source_url),
                            'bNum' => $entry->id,
                            'preUnit' => $this->entity,
                            'exdata' => '|\'entry_id\':'.$entry->id.'|',
                        ];
                    } else {
                        Log::error(self::LOG_TAG.'video path of entry['.$entry->id.'] is empty');

                        $error_evidenced_entry_ids[] = $entry->id;
                    }
                }
            }

            if (empty($filelist)) {
                Log::info(self::LOG_TAG.'all the entries have evidence');
            } else {
                $sync_file_info = $anZhengEvidenceService->syncFileInfo($filelist);
                if ('error' == $sync_file_info['status']) {
                    Log::error(self::LOG_TAG.'sync file info error. request result:'.json_encode($sync_file_info));
                    if (!empty($evidenced_entry_ids)) {
                        EntryAnzhengEvidence::whereIn('entry_id', $evidenced_entry_ids)->update(['status' => EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR]);
                    }
                } else {
                    if (!empty($evidenced_entry_ids)) {
                        EntryAnzhengEvidence::whereIn('entry_id', $evidenced_entry_ids)->update(['case_id' => $sync_file_info['data'], 'status' => EntryAnzhengEvidence::STATUS_EVIDENCE_DONE]);
                    }
                }
            }

            if (!empty($error_evidenced_entry_ids)) {
                EntryAnzhengEvidence::whereIn('entry_id', $error_evidenced_entry_ids)->update(['status' => EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR]);
            }

            $playlists = Entry::find($this->entry_ids[0])->playlists()->whereHas('evidenceRequest')->get();
            foreach ($playlists as $playlist) {
                $entries = $playlist->evidenceEntries()->with(['anzhengEvidence' => function ($query) {
                    $query->where('status', EntryAnzhengEvidence::STATUS_REQUEST_EVIDENCE);
                }])->get();
                foreach ($entries as $entry) {
                    if ($entry->anzhengEvidence) {
                        continue 2;
                    }
                }
                $evidence_request = $playlist->evidenceRequest;
                $evidence_request->status = PlaylistEvidenceRequest::STATUS_DONE;
                $evidence_request->save();
            }
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
        }
    }

    private function url_exists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //不下载
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        //设置超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 == $http_code) {
            return true;
        }

        return false;
    }
}
