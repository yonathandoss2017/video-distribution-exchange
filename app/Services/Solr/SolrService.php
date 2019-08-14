<?php

namespace App\Services\Solr;

use Illuminate\Support\Collection;
use App\Jobs\Solr\SyncPlaylistToSolrJob;
use App\Jobs\Solr\SyncPlaylistEntriesToSolrMarketplace;

class SolrService
{
    const CORE_MARKETPLACE = 'marketplace';

    const CORES = [self::CORE_MARKETPLACE];

    public static function getSolrConfig($core)
    {
        $config = Config("solarium.{$core}");

        if (empty($config)) {
            return ['error' => "[ERROR]: Core {$core} not found!"];
        }

        $default = Config('solarium.default');
        if (empty($config['endpoint'][$default])) {
            return ['error' => "[ERROR]: Core {$core} not found!"];
        }

        return $config;
    }

    public static function getAllCores()
    {
        return array_keys(array_except(config('solarium'), ['default']));
    }

    public static function getValidCores(array $cores = null): array
    {
        if (empty($cores)) {
            return self::getAllCores();
        }

        $validCores = [];
        foreach ($cores as $core) {
            $config = self::getSolrConfig($core);
            if (!isset($config['error'])) {
                $validCores[] = $core;
            }
        }

        return array_unique($validCores);
    }

    public static function isValidCore($core)
    {
        return in_array($core, self::CORES);
    }

    public static function syncAllByPlaylist(Collection $playlists)
    {
        foreach ($playlists as $playlist) {
            SyncPlaylistToSolrJob::dispatch($playlist);
            SyncPlaylistEntriesToSolrMarketplace::dispatch($playlist);
        }
    }
}
