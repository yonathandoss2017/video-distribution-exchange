<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaylistEvidenceRequest extends Model
{
    use SoftDeletes;

    const STATUS_PROCESSING = 0;
    const STATUS_DONE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['property_id', 'playlist_id', 'created_by'];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function StatusDisplay()
    {
        switch ($this->status) {
            case self::STATUS_PROCESSING:
                return 'status_processing';
            case self::STATUS_DONE:
                return 'status_done';
        }

        return 'unkown';
    }
}
