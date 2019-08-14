<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryFingerprint extends Model
{
    use SoftDeletes;

    protected $table = 'entry_fingerprints';

    protected $fillable = ['entry_id'];

    const STATUS_FINGERPRINT_EXTRACTION_FAILED = 0;
    const STATUS_FINGERPRINT_EXTRACTION_PROCESSING = 1;
    const STATUS_FINGERPRINT_EXTRACTION_SUCCESS = 2;
}
