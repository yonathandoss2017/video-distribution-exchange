<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryScene extends Model
{
    use SoftDeletes;

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id',
        'name',
        'description',
        'image_path',
        'start_in_seconds',
        'end_in_seconds',
        'dpp_duration',
        'type',
        'locale',
        'suitable',
        'blacklist',
        'keywords',
        'created_at',
        'updated_at',
    ];

    /**
     * types.
     */
    const TYPE_SIGNAGE = 'signage';
    const TYPE_PRODUCT = 'product';
    const TYPE_VIDEO = 'video';
    const TYPE_LOGO = 'logo';

    /**
     * Relationships.
     */

    /**
     * scene belong to an entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    /**
     * Public function.
     */
    public static function getTypes()
    {
        return [
            self::TYPE_SIGNAGE,
            self::TYPE_PRODUCT,
            self::TYPE_VIDEO,
            self::TYPE_LOGO,
        ];
    }
}
