<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformAlivod extends Model
{
    use SoftDeletes;

    const STATUS_ERROR = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_UPLOAD_QUEUED = 2;
    const STATUS_UPLOAD_COMPLETE = 3;
    const STATUS_UPLOAD_FAILED = 4;
    const STATUS_TRANSCODE_COMPLETE = 5;
    const STATUS_TRANSCODE_FAILED = 6;
    const STATUS_READY = 5;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'platform_alivods';

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'video_id',
        'cover_id',
        'file_size_in_byte',
        'disk_space_in_byte',
        'status',
        'job_id',
    ];

    /**
     * iVideoStream entry belong to one IVX entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function transcodes()
    {
        return $this->hasMany(PlatformAlivodTranscode::class);
    }

    public function delete()
    {
        $this->transcodes()->delete();

        return parent::delete();
    }

    public function isReady()
    {
        return self::STATUS_READY == $this->status;
    }

    public function getVideoData()
    {
        if (!$this->isReady()) {
            return null;
        }

        return [
            'id' => $this->entry_id,
            'video_id' => $this->video_id,
            'cover_id' => $this->cover_id,
            'platform' => Entry::PLATFORM_ALIVOD,
            'download_url' => $this->source_url,
        ];
    }
}
