<?php

namespace App\Services;

use App\Models\Playlist;

class PlaylistService
{
    const LOG_TAG = '[services:playlist]: ';

    public static function deletePlaylist(int $id)
    {
        $status = 'success';
        $message = __('manage/cp/contents/playlists.playlist_is_deleted_successfully');

        $playlist = Playlist::where('id', '=', $id)
            ->first();

        if (!isset($playlist)) {
            $status = 'warning';
            $message = 'Playlist Not Found';
        } else {
            $playlist->delete();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    public static function newPlaylistValidator(): array
    {
        $validate_field = [
            'name' => 'required|max:255',
            'genre' => 'required',
            'region' => 'required',
            'language' => 'required',
        ];

        return array_merge($validate_field, FeaturedImageService::getValidator());
    }

    public static function validatePlaylistPublished($validator, $request)
    {
        $validator->sometimes('publish_start_date', 'required|date', function ($input) {
            return empty($input->available_now);
        });
        $validator->sometimes('publish_end_date', 'required|date', function ($input) {
            return empty($input->until_forever);
        });
        if (!empty($request->available_now)) {
            $validator->sometimes('publish_end_date', 'after:now_date', function ($input) {
                return empty($input->until_forever);
            });
        } else {
            $validator->sometimes('publish_end_date', 'after:publish_start_date', function ($input) {
                return empty($input->until_forever);
            });
        }

        return $validator;
    }
}
