<?php

namespace App\Models;

use Carbon\Carbon;
use App\Events\PlaylistDeletedEvent;
use App\Events\PlaylistUpdatedEvent;
use App\Events\PlaylistUpdatingEvent;
use App\Jobs\PlaylistEntryDeletionJob;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\PlaylistRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class Playlist extends Model
{
    use SoftDeletes;

    const DEFAULT_THUMBNAIL_WIDTH = 600;

    // status DPP
    const DPP_STATUS_PROCESSING = 1;
    const DPP_STATUS_REVIEW = 2;
    const DPP_STATUS_PUBLISHED = 3;

    // status Playlist Deletion
    const STATUS_DELETE_PROCESSING = 99;
    const STATUS_DELETE_FAILED = 100;

    //Playlist status
    const STATUS_PENDING = 0;
    const STATUS_READY = 1;
    const STATUS_REJECTED = 2;
    const STATUS_DRAFT = 3;

    // Playlist publish status
    const PUBLISH_STATUS_UNPUBLISH = 0;
    const PUBLISH_STATUS_REVIEW = 1;
    const PUBLISH_STATUS_PUBLISHED = 2;
    const PUBLISH_STATUS_REJECTED = 3;

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at', 'dpp_created_at', 'dpp_updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'property_id', 'name', 'description', 'genre', 'region', 'video_type', 'language', 'publish', 'publish_start_date', 'publish_end_date', 'thumbnail_path', 'dpp_status', 'dpp_created_at', 'dpp_updated_at', 'stars', 'priority', 'tom_id', 'publish_status',
    ];

    //Hide certain attributes from response
    protected $hidden = ['pivot'];

    //Attribute casting
    protected $casts = [
        'publish' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'deleted' => PlaylistDeletedEvent::class,
        'saved' => PlaylistUpdatedEvent::class,
        'updating' => PlaylistUpdatingEvent::class,
    ];

    /**
     * RelationShip: playlist belong to property(cp).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *                                                           DEPRECATED, TO-BE-REMOVED, please use contentProvider()
     */
    public function content_provider()
    {
        return $this->contentProvider();
    }

    public function contentProvider()
    {
        return $this->belongsTo(PropertyCP::class, 'property_id', 'id');
    }

    public function propertyPlaylist()
    {
        //DO NOT USE, use PlaylistProperty::findRecord() instead!
        /*return $this->belongsToMany(PropertySP::class, 'playlist_property', 'playlist_id', 'property_id')
            ->withPivot('thumbnail_path', 'playlist_name')
            ->withTimestamps();*/
    }

    /**
     * RelationShip: playlist has many entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries()
    {
        return $this->belongsToMany(Entry::class);
    }

    /**
     * RelationShip: playlist has many DPP entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dpp_entries()
    {
        return $this->belongsToMany(Entry::class)->wherePivot('dpp_requested', true);
    }

    public function readyEntries()
    {
        return $this->belongsToMany(Entry::class)->where('status', '=', Entry::STATUS_READY);
    }

    public function publishedEntries()
    {
        return $this->belongsToMany(Entry::class)->published();
    }

    public function evidenceEntries()
    {
        return $this->belongsToMany(Entry::class)->whereHas('anzhengEvidence');
    }

    public function evidenceRequest()
    {
        return $this->hasOne(PlaylistEvidenceRequest::class);
    }

    public function marketplaceTerm()
    {
        return $this->belongsTo(TermsOfMarketplace::class, 'tom_id');
    }

    public function userCarts()
    {
        return $this->hasMany(UserCart::class);
    }

    /**
     * TODO: rename same as model name TermsOfDistribution.
     */
    public function tods()
    {
        return $this->belongsToMany(TermsOfDistribution::class, 'playlist_terms_of_distribution', 'playlist_id', 'tod_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Return TODs that has status active only.
     */
    public function activeTods()
    {
        return $this->tods()
            ->where('status', TermsOfDistribution::STATUS_ACTIVE)
            ->wherePivot('deleted_at', null)
            ->latest('published_at');
    }

    /**
     * Return TODs that has status active and the availability period still in range.
     */
    public function availableTods()
    {
        return $this->activeTods()
        ->whereHas('regionRights', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('started_at');
                $q->whereNull('ended_at');
            });
            $q->orWhere(function ($q) {
                //Make the current time to end of the day so we can omit whatever time the started_at is
                $q->where('started_at', '<=', Carbon::now()->hour(23)->minute(59)->second(59));
                //Make the current time to start of the day so we can omit whatever time the ended_at is
                $q->where('ended_at', '>=', Carbon::now()->hour(0)->minute(0)->second(0));
            });
        });
    }

    public function scopeOnScheduled($query)
    {
        $today_time = Carbon::today()->toDateTimeString();

        return $query->where(function ($query) use ($today_time) {
            $query->whereNull('publish_start_date')->orWhere('publish_start_date', '<=', $today_time);
        })->where(function ($query) use ($today_time) {
            $query->whereNull('publish_end_date')->orWhere('publish_end_date', '>=', $today_time);
        });
    }

    public function scopePublished($query)
    {
        return $query->where('publish_status', self::PUBLISH_STATUS_PUBLISHED);
    }

    public function getStartDateAttribute($publish_start_date)
    {
        if ($publish_start_date) {
            return Carbon::parse($publish_start_date)->toDateString();
        }

        return $publish_start_date;
    }

    public function getEndDateAttribute($publish_end_date)
    {
        if ($publish_end_date) {
            return Carbon::parse($publish_end_date)->toDateString();
        }

        return $publish_end_date;
    }

    /**
     * Delete playlist and playlist related data.
     *
     * @return bool|null
     */
    public function delete()
    {
        $entries = Entry::join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
            ->whereIn('entry_id', $this->entries()->pluck('entry_id'))
            ->get()
            ->groupBy('id')
            ->filter(function ($value, $key) {
                return 1 == $value->count();
            });
        if ($entries->count() < 50 && $entries->count() > 0) {
            foreach ($entries as $entry) {
                $entry->first()->delete();
            }
        } elseif ($entries->count() >= 50) {
            $this->status = self::STATUS_DELETE_PROCESSING;
            $this->save();
            dispatch(new PlaylistEntryDeletionJob($this));

            return true;
        }
        //entry detach has done in entry delete
        $this->entries()->detach();
        PlaylistProperty::deleteRecordsByPlaylist($this);
        $this->evidenceRequest()->delete();
        if (parent::delete()) {
            event(new PlaylistUpdatedEvent($this));

            return true;
        } else {
            return false;
        }
    }

    /**
     * Check playlist's license status.
     *
     * @param $property_id
     *
     * @return string
     */
    public function checkLicenseStatus($property_id)
    {
        $pp = PlaylistProperty::where('playlist_id', $this->id)->where('property_id', $property_id)->first();
        if (isset($pp)) {
            return $pp->status;
        }

        return 'N/A';
    }

    /**
     * Get SP's playlist name.
     *
     * @param $property_id
     */
    public function getSpPlaylistName($property_id)
    {
        $pp = PlaylistProperty::where('playlist_id', $this->id)->where('property_id', $property_id)->first();
        if (isset($pp)) {
            return $pp->playlist_name;
        }

        return null;
    }

    /**
     * Relationship:.
     *
     * @return
     */
    public function latestReadyEntry()
    {
        return $this->readyEntries()->latest()->first();
    }

    public function loadEntries($max, $afterId = 0)
    {
        return $this->load(['entries' => function ($q) use ($afterId, $max) {
            $q->orderBy('entry_playlist.entry_id')
                ->where('entry_playlist.entry_id', '>', $afterId)
                ->take($max);
        }]);
    }

    public function formattedStartDate()
    {
        if (!$this->publish_start_date) {
            return null;
        }

        return Carbon::parse($this->publish_start_date)->format('d M, Y');
    }

    public function formattedEndDate()
    {
        if (!$this->publish_end_date) {
            return null;
        }

        return Carbon::parse($this->publish_end_date)->format('d M, Y');
    }

    public function termsOfDistributionServiceProviders()
    {
        return app()->make(PlaylistRepository::class)
            ->getServiceProviders($this);
    }

    public function termsOfDistributionServiceProvidersCount()
    {
        return $this->termsOfDistributionServiceProviders()
            ->count();
    }

    /**
     * Get current active tod by given sp id.
     *
     * @param int $sp_id
     *
     * @return \App\Models\TermsOfDistribution
     */
    public function getActiveTodBySp($sp_id)
    {
        return $this->activeTods
            ->where('sp_property_id', $sp_id)
            ->sortByDesc('publised_at') // we will use latest active tod
            ->first();
    }

    /**
     * To get all playlists whitelisted to specific SP (internal whitelist + external whitelist) that have availability still in range, primaryly to be use in API.
     */
    public function scopeWhitelistedForSP($query, PropertySP $sp)
    {
        $query->where('status', Playlist::STATUS_READY)
            ->where(function ($q) use ($sp) {
                return $q->where(function ($q) use ($sp) {
                    $q->internalWhitelisted($sp);
                })->orWhere(function ($q) use ($sp) {
                    $q->externalWhitelisted($sp, false);
                });
            });

        //Filter out expired playlists
        return $query->whereNotIn('id', $this->notAvailableForSP($sp, $query->get()));
    }

    /**
     * The current SP is an internal whitelist before it is distributed.
     *
     * @param $query
     * @param PropertySP $sp
     *
     * @return mixed
     */
    public function scopeInternalWhitelisted($query, PropertySP $sp)
    {
        return $query->whereHas('contentProvider', function ($q) use ($sp) {
            $q->whereHas('internalTod', function ($query) use ($sp) {
                $query->whereHas('serviceProviders', function ($q1) use ($sp) {
                    $q1->where('property_id', $sp->id);
                });
            });
        });
    }

    public function scopeExternalWhitelisted($query, PropertySP $sp, bool $checkAvailability)
    {
        return $query->when($checkAvailability,
        function ($q) use ($sp) {
            $q->whereHas('availableTods', function ($q) use ($sp) {
                $q->where('sp_property_id', $sp->id);
            });
        },
        function ($q) use ($sp) {
            $q->whereHas('activeTods', function ($q) use ($sp) {
                $q->where('sp_property_id', $sp->id);
            });
        });
    }

    /**
     * Filter the content playlists that have expired in the distribution terms.
     *
     * @param PropertySP $sp
     *
     * @return array
     */
    public function notAvailableForSP(PropertySP $sp, $playlists)
    {
        //Get external whitelist playlists from playlists that belongs to tods
        $external_whitelist_playlists = Playlist::whereHas('activeTods', function ($query) use ($sp) {
            $query->where('sp_property_id', $sp->id);
        })->with(['activeTods' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }, 'activeTods.regionRights'])
            ->whereIn('id', $playlists->pluck('id')->toArray())
            ->get();

        //Judge whether the latest TOD has expired from the TOD of the external whitelist playlist.
        $not_available_playlist_ids = [];
        foreach ($external_whitelist_playlists as $playlist) {
            $first_active_tod = $playlist->activeTods[0];
            if ($first_active_tod->regionRights[0]->ended_at < Carbon::now()->hour(0)->minute(0)->second(0)) {
                $not_available_playlist_ids[] = $playlist->id;
            }
        }

        return $not_available_playlist_ids;
    }

    /**
     * Get dpp status.
     *
     * @return string, can be used as index of lang
     */
    public function dppStatusDisplay()
    {
        switch ($this->dpp_status) {
            case self::DPP_STATUS_REVIEW:
                return 'status_review';
            case self::DPP_STATUS_PROCESSING:
                return 'status_processing';
            case self::DPP_STATUS_PUBLISHED:
                return 'status_published';
        }

        return 'unknown';
    }
}
