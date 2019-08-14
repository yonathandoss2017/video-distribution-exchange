<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryBase extends Model
{
    use SoftDeletes;

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'published_at', 'indexed_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'media_type', 'duration', 'thumbnail_url', 'platforms', 'status', 'views', 'image_path',
        'created_at', 'updated_at', 'published_at', 'allowed_in', 'blocked_in', 'published', 'indexed_at', 'metaable_type', 'metaable_id', 'indexed_at_marketplace', 'source', 'produced_at',
    ];

    // Hide certain attributes from response
    protected $hidden = ['pivot'];

    // Casts to another type
    protected $casts = [
        'published' => 'boolean',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['playlists'];

    const META_TYPE_NONE = 1;
    const META_TYPE_MUSIC = 2;
    const META_TYPES = [
        'music_video' => self::META_TYPE_MUSIC,
    ];

    /**
     * media type.
     */
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_AUDIO = 'audio';
    const MEDIA_TYPE_IMAGE = 'image';

    /**
     * entry status.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PENDING = 'pending';
    const STATUS_READY = 'ready';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ERROR = 'error';

    /**
     * entry analyze status.
     */
    const ANALYZE_STATUS_NOT_START = 0;
    const ANALYZE_STATUS_PROCESSING = 1;
    const ANALYZE_STATUS_READY = 2;
    const ANALYZE_STATUS_FAILED = 3;

    const PLATFORM_IVIDEOSTREAM = 'ivideostream';
    const PLATFORM_ALIVOD = 'alivod';

    /**
     * Available Platforms
     * PLATFORMS are sorted in order of highest priority.
     */
    const PLATFORMS = [
        self::PLATFORM_ALIVOD,
    ];

    /**
     * player type.
     */
    const PLAYER_TYPE_IFRAME = 'ifrmae';
    const PLAYER_TYPE_JAVASCRIPT = 'javascript';

    // DPP status
    const DPP_STATUS_PROCESSING = 'processing';
    const DPP_STATUS_REVIEW = 'review';
    const DPP_STATUS_PUBLISHED = 'published';

    // source value
    const SOURCE_DIRECT_UPLOAD = 'direct_upload';
    const SOURCE_API = 'api';
    const SOURCE_DIRECT_OSS = 'direct_oss';

    //video upload method
    const VIDEO_DIRECT_UPLOAD = 'direct_oss';
    const VIDEO_OSS_URL = 'oss_url';

    public static function not_ready_status()
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PROCESSING,
            self::STATUS_PENDING,
            self::STATUS_ERROR,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * RelationShip: entry belong a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * DEPRECATED, please use content_provider_property instead
     * RelationShip: entry belong a CP(content provider).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content_provider()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * RelationShip: entry belong a CP(content provider).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content_provider_property()
    {
        return $this->belongsTo(PropertyCP::class, 'property_id');
    }

    /**
     * RelationShip: entry has one metadata.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function metadata()
    {
        return $this->hasOne(EntryMeta::class);
    }

    /**
     * RelationShip: entry has many localizations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function localizations()
    {
        return $this->hasMany(EntryLocalization::class);
    }

    /**
     * RelationShip: entry has many analytics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analytics()
    {
        return $this->hasMany(EntryAnalytic::class);
    }

    /**
     * RelationShip: entry has many subtitles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtitles()
    {
        return $this->hasMany(EntrySubtitle::class);
    }

    /**
     * RelationShip: entry has many DPP scenes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenes()
    {
        return $this->hasMany(EntryScene::class);
    }

    /**
     * RelationShip: entry belong to many playlists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class);
    }

    public function anzhengEvidence()
    {
        return $this->hasOne(EntryAnzhengEvidence::class);
    }

    public function platformAlivod()
    {
        return $this->hasOne(PlatformAlivod::class);
    }

    public function platformAlivodTranscodes()
    {
        return $this->hasManyThrough(PlatformAlivodTranscode::class, PlatformAlivod::class, 'entry_id', 'platform_alivod_id');
    }

    public function aiReviewResult()
    {
        return $this->hasOne(EntryAiReviewResult::class);
    }

    public function aiReviewVideoResult()
    {
        return $this->hasManyThrough(AiReviewVideoResult::class, EntryAiReviewResult::class, 'entry_id', 'review_id');
    }

    public function fingerprint()
    {
        return $this->hasOne(EntryFingerprint::class);
    }

    /**
     * For entry_property table.
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class)->withPivot('title', 'description', 'image_path')->withTimestamps();
    }

    /**
     * RelationShip: entry may have meta data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function metaable()
    {
        return $this->morphTo();
    }
}
