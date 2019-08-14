<?php

namespace App\Models;

use Exception;
use Carbon\Carbon;
use App\Events\EntryUpdatedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Repositories\EntryRepository;
use App\Events\VideoAddedToPlaylistEvent;

class Entry extends EntryBase
{
    protected $dispatchesEvents = [
        'saved' => EntryUpdatedEvent::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Entry $entry) {
            if (!isset($entry->published_at)) {
                $entry->published_at = Carbon::now();

                return $entry;
            }
        });

        static::saving(function (Entry $entry) {
            $entry->indexed_at = null;

            return $entry;
        });
    }

    /**
     * Scope a query to only include published entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReady($query)
    {
        return $query->where('status', static::STATUS_READY);
    }

    /**
     * Scope a query to only include published entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotIn('status', self::not_ready_status())
            ->where(function ($q) {
                return $q->where('published_at', '<=', Carbon::now())->orWhere('published_at', null);
            });
    }

    public function scopeRequestFilter($query, $search, $status, $start_date, $end_date)
    {
        return $query->when($search, function ($query) use ($search) {
            return $query->where('entries.name', 'like', '%'.$search.'%');
        })
            ->when(self::STATUS_READY == $status, function ($query) use ($status) {
                return $query->where('entries.status', self::STATUS_READY)->published();
            })
            ->when(self::STATUS_DRAFT == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_DRAFT);
            })
            ->when(self::STATUS_PROCESSING == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_PROCESSING);
            })
            ->when(self::STATUS_PENDING == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_PENDING);
            })
            ->when(self::STATUS_REJECTED == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_REJECTED);
            })
            ->when(self::STATUS_SCHEDULED == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_READY)->notPublished();
            })
            ->when(self::STATUS_ERROR == $status, function ($query) {
                return $query->where('entries.status', self::STATUS_ERROR);
            })
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('entries.updated_at', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('entries.updated_at', '<=', Carbon::parse($end_date)->format('Y-m-d'));
            });
    }

    /**
     * Scope a query to only include not published entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotPublished($query)
    {
        return $query->where('published_at', '>', Carbon::now())
            ->orWhere(function ($q) {
                return $q->whereIn('status', self::not_ready_status());
            });
    }

    /**
     * Scope a query to check is scheduled.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getIsPendingAttribute()
    {
        return $this->published_at > Carbon::now();
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getProducedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if ('zh' == App::getLocale()) {
            return Carbon::parse($value)->formatLocalized('%Y年%m月%d日');
        } elseif ('en' == App::getLocale()) {
            return Carbon::parse($value)->formatLocalized('%m/%d/%Y');
        }
    }

    /**
     * @param $value
     */
    public function setProducedAtAttribute($value)
    {
        if ($value) {
            $produced_at = str_replace(['年', '月', '日'], '-', $value);
            $this->attributes['produced_at'] = Carbon::parse(rtrim($produced_at, '-'))->toDateString();
        } else {
            $this->attributes['produced_at'] = null;
        }
    }

    public function getSourceUrlAttribute()
    {
        $source_url = optional($this->platformAlivod)->source_url;
        if ($source_url) {
            $source_url = str_replace(config('alivod.endpoint'), config('alivod.cname_endpoint'), $source_url);
        }

        return $source_url;
    }

    public function getSourceNameAttribute()
    {
        $extension = '';
        if (optional($this->platformAlivod)->source_url) {
            $extension = pathinfo($this->platformAlivod->source_url)['extension'];
        }

        return $this->name.'.'.$extension;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPlatformVideos($query)
    {
        return EntryRepository::prepareEntryQueryWithPlatformVideos($query);
    }

    public function scopeWhereHasPlatformVideos($query)
    {
        return EntryRepository::prepareEntryQueryWhereHasPlatformVideos($query);
    }

    public function getSpEntryTimestamp($property_id)
    {
        $spEntry = $this->properties()->where('property_id', $property_id)->first();

        return $spEntry->pivot->updated_at->timestamp ?? $this->updated_at->timestamp;
    }

    /**
     * Get relationship name from give platforms.
     *
     * @param $platform
     *
     * @return string|null
     */
    public static function getRelationNameFromPlatform($platform)
    {
        $platform = strtolower($platform);
        if (self::isValidPlatform($platform)) {
            return 'platform'.ucfirst($platform);
        }

        return null;
    }

    /**
     * Find entry by platform id in specific property.
     *
     * @param $property_id
     * @param $platform: Entry::PLATFORM_YOUTUBE/Entry::PLATFORM_DAILYMOTION/Entry::PLATFORM_IVIDEOSTREAM
     * @param $platform_id: video id for the platform
     *
     * @return Entry or null if not found
     */
    public static function findByPlatformAndProperty($property_id, $platform, $platform_id)
    {
        $relation = self::getRelationNameFromPlatform($platform);
        if (null === $relation) {
            return null;
        }

        return self::where('property_id', $property_id)
            ->whereHas($relation, function ($query) use ($platform_id, $relation) {
                $query->where('video_id', '=', $platform_id);
            })
            ->first();
    }

    /**
     * Create entry with platforms.
     *
     * @param $property_id
     * @param $user_id
     * @param array $entry_attributes, can be [name, description, duration, thumbnail_url] , "name" must provide
     * @param array $meta_attributes,  can be [tags, video_type, director, stars, genre, region, privacy], null is accepted
     * @param $platform: Entry::PLATFORM_YOUTUBE/Entry::PLATFORM_DAILYMOTION/Entry::PLATFORM_IVIDEOSTREAM
     * @param array $platform_attributes, depends on platform type:
     *                                    thumbnail_url, download_url, data_url, status], all must provide
     *                                    platformAlivod: can be [video_id, source_url], all are optional
     *
     * @return Entry or null if something error
     */
    public static function createWithPlatform(
        $property_id,
        $user_id,
        $entry_attributes,
        $meta_attributes,
        $platform,
        $platform_attributes
    ) {
        DB::beginTransaction();
        try {
            $entry = new self(
                array_merge([
                    'platforms' => $platform,
                    'media_type' => self::MEDIA_TYPE_VIDEO,
                ], $entry_attributes)
            );
            if (empty($entry->thumbnail_url) && array_key_exists('thumbnail_url', $platform_attributes)) {
                $entry->thumbnail_url = $platform_attributes['thumbnail_url'];
            }
            $entry->owner()->associate($user_id);
            $entry->content_provider()->associate($property_id);
            $entry->save();

            //attach metadata
            if (!empty($meta_attributes)) {
                $entry->metadata()->create($meta_attributes);
            }

            $relation = self::getRelationNameFromPlatform($platform);
            if (null === $relation) {
                DB::rollBack();

                return null;
            }

            $entry->$relation()->create($platform_attributes);
            DB::commit();

            return $entry;
        } catch (Exception $e) {
            DB::rollBack();
        }

        return null;
    }

    /**
     * Check if entry is on platform or not.
     *
     * @param $platform
     *
     * @return bool
     */
    public function hasPlatform($platform)
    {
        return false !== stripos($this->platforms, $platform);
    }

    public function getFormattedDuration()
    {
        return gmdate('H:i:s', $this->duration);
    }

    /**
     * Delete entry and entry related data.
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->metadata()->delete();
        $this->localizations()->delete();
        $this->analytics()->delete();
        $this->subtitles()->delete();
        $this->platformAlivod()->delete();
        $this->aiReviewResult()->delete();
        $this->playlists()->detach();
        $this->properties()->detach();
        if (parent::delete()) {
            event(new EntryUpdatedEvent($this));

            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate supported platform.
     *
     * @param $platform
     *
     * @return bool
     */
    public static function isValidPlatform($platform)
    {
        if (!in_array($platform, self::PLATFORMS)) {
            return false;
        }

        return true;
    }

    /**
     * @param Playlist|null $playlistUpdating
     *
     * @return bool
     */
    public function hasPublishedPlaylist(Playlist $playlistUpdating = null)
    {
        $playlists = $this->playlists;
        if (!count($playlists)) {
            return false;
        }

        foreach ($playlists as $playlist) {
            if ($playlistUpdating && ($playlist->id == $playlistUpdating->id)) {
                if (Playlist::PUBLISH_STATUS_PUBLISHED == $playlistUpdating->publish_status) {
                    return true;
                }
                continue;
            }

            if (Playlist::PUBLISH_STATUS_PUBLISHED == $playlist->publish_status) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find Sp video by entry_id and playlist ids.
     *
     * @param $entryId
     * @param array $spPlaylistIds
     *
     * @return mixed
     */
    public function findSpEntryByIdAndPlaylist($entryId, array $spPlaylistIds)
    {
        return self::where('id', $entryId)
            ->whereHas('playlists', function ($q) use ($spPlaylistIds) {
                $q->whereIn('id', $spPlaylistIds);
            })->first();
    }

    /*
     * Get highest priority from the playlists this entry belongs to
     */
    public function getPriority()
    {
        //priority : 2=lowest, 1=normal, 0=highest
        $priority = 2;
        foreach ($this->playlists as $playlist) {
            if ($priority > $playlist->priority) {
                $priority = $playlist->priority;
            }
            if (0 == $priority) {
                break;
            }
        }

        return $priority;
    }

    /**
     * @param array | Playlist $playlist
     */
    public function addToPlaylist($playlist)
    {
        if (is_array($playlist)) {
            $playlist_objs = Playlist::whereIn('id', $playlist)->get();
            foreach ($playlist_objs as $playlist_obj) {
                try {
                    $this->addToPlaylist($playlist_obj);
                } catch (Exception $e) {
                    Log::info(self::LOG_TAG.'Error = '.$e->getMessage());
                }
            }
        } else {
            try {
                if ((int) $this->property_id !== (int) $playlist->property_id) {
                    throw new Exception('Cannot add entry to playlist which belongs to other property');
                }

                $this->playlists()->attach($playlist->id);
                $playlist->touch();
                event(new VideoAddedToPlaylistEvent($this, $playlist));
            } catch (Exception $e) {
                // everything is fine
            }
        }
    }

    public function getGenre()
    {
        $genre = [];
        $playlists = $this->playlists;
        foreach ($playlists as $playlist) {
            if (null !== $playlist->genre) {
                $genre[] = $playlist->genre;
            }
        }

        return array_unique($genre);
    }

    //platforms accessor, will be called by laravel automatically when try to access it like $entry->platforms
    public function getPlatformsAttribute($value)
    {
        $platforms = explode(',', $value);
        $result = [];
        foreach ($platforms as $platform) {
            $platform = strtolower(trim($platform));
            if (self::isValidPlatform($platform)) {
                $result[] = $platform;
            }
        }
        if (!empty($result)) {
            return implode(',', array_unique($result));
        }

        return null;
    }

    public function updateSubtitles($subtitles)
    {
        $savedSubtitles = $this->subtitles()->withTrashed()->get();
        foreach ($subtitles as $key => $subtitle) {
            $findSubtitle = $savedSubtitles->first(function ($item) use ($key, $subtitle) {
                return $item->lang == $key;
            });
            if ($findSubtitle) {
                $findSubtitle->url = $subtitle;
                if ($findSubtitle->trashed()) {
                    $findSubtitle->restore();
                } else {
                    $findSubtitle->save();
                }
            }
        }
    }
}
