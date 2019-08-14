<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngestionAnalytic extends Model
{
    protected $table = 'ingestion_analytics';
    protected $fillable = [
        'date', 'youtube_success', 'youtube_failed', 'direct_success', 'direct_failed', 'rss_success', 'rss_failed',
    ];
}
