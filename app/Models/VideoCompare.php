<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCompare extends Model
{
    use SoftDeletes;

    protected $table = 'video_compares';

    protected $fillable = ['property_id', 'title', 'video_url', 'job_id', 'status'];

    const STATUS_FAILED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_FINISHED = 2;
    const STATUS = ['failed', 'processing', 'finished'];

    public function compareResults()
    {
        return $this->hasMany(VideoCompareResult::class, 'compare_id');
    }
}
