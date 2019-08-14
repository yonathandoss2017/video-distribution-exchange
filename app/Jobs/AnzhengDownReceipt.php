<?php

namespace App\Jobs;

use Log;
use App\Models\Entry;
use Mockery\Exception;
use Illuminate\Bus\Queueable;
use App\Models\EntryAnzhengEvidence;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Blockchain\AnZhengEvidenceService;

class AnzhengDownReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $entry_ids;

    const LOG_TAG = '[down receipt] ';

    /**
     * Create a new job instance.
     */
    public function __construct($entry_ids)
    {
        $this->entry_ids = $entry_ids;
    }

    /**
     * Execute the job.
     */
    public function handle(AnZhengEvidenceService $anZhengEvidenceService)
    {
        try {
            $entries = Entry::whereIn('id', $this->entry_ids)->with('anzhengEvidence')->get();
            foreach ($entries as $entry) {
                if (empty($entry->anzhengEvidence)) {
                    continue;
                }
                if (EntryAnzhengEvidence::STATUS_EVIDENCE_READY == $entry->anzhengEvidence->status) {
                    continue;
                }
                $receipts = $anZhengEvidenceService->downReceipt($entry);
                if ('success' == $receipts['status']) {
                    foreach ($receipts['data'] as $receipt) {
                        EntryAnzhengEvidence::where('entry_id', $receipt['exdatacode'])->update(['receipt' => $receipt['downLoadurl'], 'status' => EntryAnzhengEvidence::STATUS_EVIDENCE_READY]);
                    }
                } else {
                    EntryAnzhengEvidence::where('case_id', $entry->anzhengEvidence->case_id)->update(['status' => EntryAnzhengEvidence::STATUS_REQUEST_DOWNLOAD_ERROR]);
                }
            }
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
        }
    }
}
