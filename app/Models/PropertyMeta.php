<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyMeta extends Model
{
    use SoftDeletes;

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
        'meta_name',
        'meta_value',
    ];

    /**
     * predefined meta name for secret.
     */
    const META_NAME_SECRET = 'secret';

    /**
     * predefined meta name for hotspot longId (associated with a unique SP).
     */
    const META_NAME_HOTSPOT_OWNER_LONGID = 'hsowner_longid';

    /**
     * Relationship: metadata belong to a property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function getValue($propertyId, $metaName)
    {
        return self::where([
            'property_id' => $propertyId,
            'meta_name' => $metaName,
        ])->value('meta_value');
    }
}
