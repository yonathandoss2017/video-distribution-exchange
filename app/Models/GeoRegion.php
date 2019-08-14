<?php

namespace App\Models;

use Webpatser\Countries\Countries;
use Illuminate\Database\Eloquent\Model;

class GeoRegion extends Model
{
    const REGION_GLOBAL_CODE = '001';

    /**
     * Local scopes.
     */

    /**
     * Scope a query to exclude sub regions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegionsExceptGlobal($query)
    {
        return $query->whereNull('parent_id')->where('region_code', '<>', self::REGION_GLOBAL_CODE);
    }

    /**
     * Relationships.
     */

    /**
     * GeoRegion has many sub region.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subRegions()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * GeoRegion belong to a region.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parentRegion()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * GeoRegion has many countries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function countries()
    {
        return $this->hasMany(Countries::class, 'sub_region_code', 'region_code');
    }

    public static function getAll($locale = null)
    {
        $regions = self::where('region_code', self::REGION_GLOBAL_CODE)->get();
        $subRegions = self::with([
            'subRegions' => function ($query) {
                $query->orderBy('area');
            },
            'subRegions.countries' => function ($query) {
                $query->orderBy('name');
            },
            ])->regionsExceptGlobal()->orderBy('area')->get();

        $regions = $regions->merge($subRegions);
        if ($locale) {
            return $regions->map(function ($region) use ($locale) {
                $region->area = __('region.'.$region->region_code, [], $locale);
                $region->subRegions = $region->subRegions->map(function ($subRegion) use ($locale) {
                    $subRegion->area = __('region.'.$subRegion->region_code, [], $locale);

                    $subRegion->countries = $subRegion->countries->map(function ($country) use ($locale) {
                        $country->name = __('country.'.$country->iso_3166_2, [], $locale);

                        return $country;
                    });

                    return $subRegion;
                });

                return $region;
            });
        }

        return $regions;
    }
}
