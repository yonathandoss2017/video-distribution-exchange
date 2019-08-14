<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyAnalytic extends Model
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
    protected $fillable = ['property_id', 'name', 'site', 'page', 'highest_active_user', 'active_user'];

    /**
     * Relationships.
     */

    /**
     * Relationship: Playlist has one App\Models\Property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
}
