<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryMeta extends Model
{
    use SoftDeletes;

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Meta is not timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tags',
        'video_type',
        'entry_id',
        'director',
        'stars',
        'genre',
        'region',
        'privacy',
    ];

    /**
     * video type list.
     *
     * @var array
     */
    public static $video_type = [
        'movie_feature',
        'movie_clip',
        'series_container',
        'series_episode',
        'series_clip',
        'standalone',
    ];

    /**
     * privacy list.
     *
     * @var array
     */
    public static $privacy = [
        'public',
        'protect',
        'private',
    ];

    /**
     * Relationship: metadata belong to an entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}
