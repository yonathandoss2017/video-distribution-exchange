<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCompareMatchFragment extends Model
{
    protected $table = 'video_compare_match_fragments';

    protected $fillable = ['compare_result_id', 'searchedVideoStartingMatchedPosition_ms', 'matchedVideoStartingMatchedPosition_ms', 'duration_ms'];
}
