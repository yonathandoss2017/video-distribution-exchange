<?php

namespace App\Repositories;

use App\Models\Entry;
use App\Models\PlatformAlivod;

class EntryRepository
{
    /**
     * @param Entry $entry
     *
     * @return array
     */
    public static function getEntryServiceProviderIds(Entry $entry)
    {
        $entry->load('playlists');

        if (!$entry->playlists->count()) {
            return [];
        }

        $serviceProviderIds = [];

        foreach ($entry->playlists as $playlist) {
            $serviceProviders = $playlist->termsOfDistributionServiceProviders()
                ->pluck('id')
                ->toArray();
            $serviceProviderIds = array_merge($serviceProviderIds, $serviceProviders);
        }

        return array_unique($serviceProviderIds);
    }

    public static function prepareEntryQueryWithPlatformVideos($query)
    {
        return $query->with(['platformAlivod' => function ($q) {
            $q->where('status', '=', PlatformAlivod::STATUS_READY);
        }]);
    }

    public static function prepareEntryQueryWhereHasPlatformVideos($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('platformAlivod', function ($q) {
                $q->where('status', '=', PlatformAlivod::STATUS_READY);
            });
        });
    }
}
