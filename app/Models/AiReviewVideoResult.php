<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiReviewVideoResult extends Model
{
    public function aiReviewResult()
    {
        return $this->belongsTo(EntryAiReviewResult::class, 'review_id');
    }
}
