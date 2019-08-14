<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformOauth extends Model
{
    use SoftDeletes;

    const GOOGLE = 'google';
    const YOUTUBE = 'youtube';
    const FACEBOOK = 'facebook';
    const DAILYMOTION = 'dailymotion';
    const IVIDEOSTREAM = 'ivideostream';
    const ALIOSS = 'alioss';

    protected $dates = ['created_at', 'updated_at', 'expires_at', 'deleted_at'];
    protected $table = 'platform_oauth';

    public function contentProviderProperty()
    {
        return $this->belongsTo(PropertyCP::class, 'property_id');
    }
}
