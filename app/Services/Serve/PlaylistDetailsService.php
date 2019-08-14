<?php

namespace App\Services\Serve;

use App\Models\Playlist;
use App\Models\PlaylistProperty;

class PlaylistDetailsService
{
    public static function getName(Playlist $playlist, $prop_id)
    {
        if ($prop_id == $playlist->property_id) {
            return $playlist->name;
        }

        $spPlaylist = PlaylistProperty::findRecord($playlist->property_id, $prop_id, $playlist->id)->first();
        if (!empty($spPlaylist)) {
            if (!empty($spPlaylist->playlist_name)) {
                return $spPlaylist->playlist_name;
            }
        }

        return $playlist->name;
    }

    public static function getActiveTodFromPlaylist(Playlist $playlist, $sp_id)
    {
        $todExternal = $playlist->getActiveTodBySp($sp_id);
        if (!$todExternal) {
            return $playlist->contentProvider->internalTod;
        }

        return $todExternal;
    }
}
