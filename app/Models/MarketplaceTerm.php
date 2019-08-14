<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceTerm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'allowed_regions',
        'excepted_regions',
        'platforms',
        'exclusivity',
        'supported_models',
        'revenue_share',
        'license_fee',
        'minimun_guarantee',
        'footnote',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getAllowedRegionsInLangAttribute()
    {
        return $this->transformToLang($this->allowed_regions, 'region.');
    }

    public function getExceptedRegionsInLangAttribute()
    {
        return $this->transformToLang($this->excepted_regions, 'region.');
    }

    public function getPlatformsInLangAttribute()
    {
        return $this->transformToLang($this->platforms, 'term.platforms.');
    }

    public function getExclusivityInLangAttribute()
    {
        return $this->transformToLang($this->exclusivity, 'term.exclusive_property.');
    }

    public function getSupportedModelsInLangAttribute()
    {
        return $this->transformToLang($this->supported_models, 'term.supported_models.');
    }

    private function transformToLang($arr, $langPath)
    {
        if ($arr) {
            $arr = explode(',', $arr);
            foreach ($arr as $item) {
                $resArr[] = __($langPath.$item);
            }

            return implode(', ', $resArr);
        } else {
            return null;
        }
    }
}
