<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributionRegionRight extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tod_id',
        'allowed_regions',
        'excepted_regions',
        'started_at',
        'ended_at',
        'payment_mode',
        'exclusivity',
        'price',
        'update_count',
        'revenue_share_cp',
        'revenue_share_sp',
        'payment_comments',
        'api_share_to',
        'download_resolution',
        'extra_terms',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function termsOfDistribution()
    {
        return $this->belongsTo(TermsOfDistribution::class, 'tod_id');
    }

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['started_at', 'ended_at', 'created_at', 'updated_at', 'deleted_at'];
    protected $touches = ['tod'];

    public function tod()
    {
        return $this->belongsTo(TermsOfDistribution::class, 'tod_id');
    }

    public function getAllowedRegionsAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return explode(',', $value);
    }

    public function getExceptedRegionsAttribute($value)
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
}
