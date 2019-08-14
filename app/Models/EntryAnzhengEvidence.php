<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EntryAnzhengEvidence extends Model
{
    const STATUS_REQUEST_EVIDENCE = 0;
    const STATUS_EVIDENCE_DONE = 1;
    const STATUS_EVIDENCE_ERROR = 2;
    const STATUS_REQUEST_DOWNLOAD = 3;
    const STATUS_EVIDENCE_READY = 4;
    const STATUS_REQUEST_DOWNLOAD_ERROR = 5;

    public function statusDisplay()
    {
        switch ($this->status) {
            case self::STATUS_REQUEST_EVIDENCE:
                return  'status_request_evidence';
            case self::STATUS_EVIDENCE_DONE:
                return  'status_evidence_done';
            case self::STATUS_EVIDENCE_ERROR:
                return 'status_evidence_error';
            case self::STATUS_REQUEST_DOWNLOAD:
                return 'status_request_download';
            case self::STATUS_EVIDENCE_READY:
                return 'status_evidence_ready';
            case self::STATUS_REQUEST_DOWNLOAD_ERROR:
                return 'status_request_download_error';
        }

        return 'unknown';
    }
}
