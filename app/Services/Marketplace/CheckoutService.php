<?php

namespace App\Services\Marketplace;

use App\Models\Playlist;

class CheckoutService
{
    const SESSION_KEY = 'marketplace_requested_playlist';

    public function addToCart($playlistId)
    {
        $playlistIds = $this->getRequestedPlaylistIds();
        if (in_array($playlistId, $playlistIds)) {
            return $playlistIds;
        }

        $playlistIds = array_merge($playlistIds, [$playlistId]);
        session()->put(self::SESSION_KEY, $playlistIds);

        return $playlistIds;
    }

    public function deleteCart()
    {
        session()->forget(self::SESSION_KEY);
    }

    public function getRequestedPlaylistCount()
    {
        return count($this->getRequestedPlaylistIds());
    }

    public function getRequestedPlaylist()
    {
        return Playlist::whereIn('id', $this->getRequestedPlaylistIds())
            ->with(['content_provider.organization'])
            ->withCount(['entries' => function ($query) {
                $query->whereHasPlatformVideos()->published();
            }])
            ->get();
    }

    public function deleteRequestedPlaylist(Playlist $playlist)
    {
        array_diff($this->getRequestedPlaylistIds(), [$playlist->id]);
        session()->put(self::SESSION_KEY, array_diff($this->getRequestedPlaylistIds(), [$playlist->id]));

        return $this->getRequestedPlaylistIds();
    }

    public function deleteAllCart()
    {
        session()->forget(self::SESSION_KEY);
    }

    public function getRequestedPlaylistIds()
    {
        if (session()->has(self::SESSION_KEY) && is_array(session()->get(self::SESSION_KEY))) {
            return session()->get(self::SESSION_KEY);
        }

        return [];
    }
}
