<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsOfMarketplace extends Model
{
    use SoftDeletes;

    protected $table = 'terms_of_marketplace';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id', 'user_id', 'name', 'region_allowed', 'region_excepted', 'payment_mode', 'price', 'update_count', 'exclusivity', 'revenue_share_cp', 'revenue_share_sp', 'payment_comments', 'api_share_to', 'download_resolution',
    ];

    public function getRegionAllowedAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return explode(',', $value);
    }

    public function getRegionExceptedAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return explode(',', $value);
    }

    public function getApiShareToAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return explode(',', $value);
    }

    public function getDownloadResolutionAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return explode(',', $value);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'tom_id');
    }
}
