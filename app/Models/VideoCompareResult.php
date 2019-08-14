<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCompareResult extends Model
{
    use SoftDeletes;

    protected $table = 'video_compare_results';

    protected $fillable = ['compare_id', 'entry_id', 'confidence', 'distortion', 'length_ms'];

    public function getDistortionAttribute($distortion)
    {
        $distortion = str_replace(' ', '_', $distortion);

        return __('manage/cp/video-compare/compare.similar_'.$distortion);
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }

    public function matchFragments()
    {
        return $this->hasMany(VideoCompareMatchFragment::class, 'compare_result_id');
    }
}
