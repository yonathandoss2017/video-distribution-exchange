<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'ivx_transcoder_profile',
    ];

    public function contentProviderProperty()
    {
        return $this->belongsTo(PropertyCP::class, 'property_id');
    }
}
