<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryAiReviewResult extends Model
{
    use SoftDeletes;

    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const SUGGESTION_BLOCK = 'block';
    const SUGGESTION_REVIEW = 'review';
    const SUGGESTION_PASS = 'pass';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry_ai_review_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['entry_id', 'jobid', 'ali_status', 'code', 'message', 'suggestion', 'abnormal_modules', 'label'];
}
