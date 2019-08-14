<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistProperty extends Model
{
    protected $table = 'playlist_property';

    protected $fillable = [
    'property_id',
    'cp_property_id',
    'playlist_id',
    'playlist_name',
    'thumbnail_path',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeFindRecord($query, $cp_id, $sp_id, $playlist_id)
    {
        return $query->where('property_id', $sp_id)
            ->where('cp_property_id', $cp_id)
            ->where('playlist_id', $playlist_id);
    }

    public static function createOrUpdateRecord($cp_id, $sp_id, $playlist_id, $extra_data = [])
    {
        $record = self::findRecord($cp_id, $sp_id, $playlist_id)->first();
        if (!$record) {
            self::create(array_merge([
            'property_id' => $sp_id,
            'cp_property_id' => $cp_id,
            'playlist_id' => $playlist_id,
            ], $extra_data));
        } elseif (!empty($extra_data)) {
            self::findRecord($cp_id, $sp_id, $playlist_id)->update($extra_data);
        }
    }

    public static function getRecordsByPlaylist(Playlist $playlist)
    {
        return self::where('playlist_id', $playlist->id)
            ->get();
    }

    public static function getRecordsByServiceProvider(PropertySP $serviceProvider)
    {
        return self::where('property_id', $serviceProvider->id)
            ->get();
    }

    public static function deleteRecordsByPlaylist(Playlist $playlist)
    {
        self::where('playlist_id', $playlist->id)
            ->delete();
    }

    public static function deleteRecordsByServiceProvider(PropertySP $serviceProvider)
    {
        self::where('property_id', $serviceProvider->id)
            ->delete();
    }
}
