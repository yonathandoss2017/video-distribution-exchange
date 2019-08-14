<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Events\PlaylistWhitelistUpdatedEvent;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsOfDistribution extends Model
{
    use SoftDeletes;

    /**
     * distribution status.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_SP_PENDING = 'pending_sp';
    const STATUS_SP_DECLINED = 'sp_declined';
    const STATUS_SP_DISCONTINUE = 'sp_discontinued';
    const STATUS_CP_REVOKED = 'cp_revoked';
    const STATUS_PLATFORM_REVIEW = 'platform_review';
    const STATUS_PLATFORM_REJECTED = 'platform_rejected';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_SP_PENDING,
        self::STATUS_SP_DECLINED,
        self::STATUS_SP_DISCONTINUE,
        self::STATUS_CP_REVOKED,
        self::STATUS_PLATFORM_REVIEW,
        self::STATUS_PLATFORM_REJECTED,
    ];

    protected $dates = [
        'published_at',
    ];

    protected $casts = [
        'sp_property_id' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cp_organization_id',
        'cp_property_id',
        'sp_property_id',
        'status',
        'name',
        'internal_remarks',
        'contract',
        'contract_name',
        'created_at',
        'updated_at',
        'published_at',
        'cp_deleted_at',
        'sp_deleted_at',
        'creator',
        'updater',
        'show_new_mark',
        'deleted_at',
    ];

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_terms_of_distribution', 'tod_id', 'playlist_id')->whereNull('playlist_terms_of_distribution.deleted_at')->withPivot('deleted_at');
    }

    public function playlistsWithTrashed()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_terms_of_distribution', 'tod_id', 'playlist_id')->withPivot('deleted_at');
    }

    public function contentProvider()
    {
        return $this->belongsTo(PropertyCP::class, 'cp_property_id');
    }

    public function getStatusColorClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'grey';
                break;
            case self::STATUS_ACTIVE:
                return 'active';
                break;
            case self::STATUS_SP_PENDING:
                return 'orange';
                break;
            case self::STATUS_SP_DECLINED:
                return 'rejected';
                break;
            case self::STATUS_CP_REVOKED:
                return 'revoked';
                break;
            case self::STATUS_SP_DISCONTINUE:
                return 'redoutline';
                break;
            case self::STATUS_PLATFORM_REVIEW:
                return 'orange';
                break;
            case self::STATUS_PLATFORM_REJECTED:
                return 'rejected';
                break;
        }
    }

    public function getContractUrlAttribute()
    {
        if (!empty($this->contract)) {
            return \App\Services\Storage\Oss\UrlService::getUrl($this->contract);
        }

        return null;
    }

    public function serviceProvider()
    {
        return $this->belongsTo(PropertySP::class, 'sp_property_id');
    }

    /**
     * internal whitelist can have many sps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function serviceProviders()
    {
        return $this->belongsToMany(PropertySP::class, 'distribution_whitelist_property', 'tod_id', 'property_id');
    }

    public function userCreator()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function userUpdater()
    {
        return $this->belongsTo(User::class, 'updater');
    }

    public function regionRights()
    {
        return $this->hasMany(DistributionRegionRight::class, 'tod_id');
    }

    public function getStatusName()
    {
        return str_replace('_', ' ', $this->status);
    }

    public function scopeExceptOwnSpTod($query)
    {
        return $query->where('sp_property_id', '<>', Property::ID_FOR_ADMIN)->orWhereNull('sp_property_id');
    }

    public function getRevocableAttribute()
    {
        if (self::STATUS_ACTIVE == $this->status || self::STATUS_SP_PENDING == $this->status) {
            return true;
        }

        return false;
    }

    public function revoke()
    {
        $previouslyActive = $this->status == $this::STATUS_ACTIVE;
        $this->status = $this::STATUS_CP_REVOKED;
        $this->save();

        $this->load(['playlists']);

        if ($previouslyActive) {
            $this->removePlaylistProperty();
        }

        return $this;
    }

    public function discontinue()
    {
        $this->status = $this::STATUS_SP_DISCONTINUE;
        $this->save();
        $this->removePlaylistProperty();

        return $this;
    }

    private function removePlaylistProperty()
    {
        $this->load(['playlists']);
        $playlists = $this->playlists;
        foreach ($playlists as $playlist) {
            $hasActiveTod = self::whereHas('playlists', function ($q) use ($playlist) {
                $q->where('id', $playlist->id);
            })
                ->where('status', $this::STATUS_ACTIVE)
                ->where('sp_property_id', $this->sp_property_id)
                ->exists();
            if (!$hasActiveTod) {
                DB::table('playlist_property')
                    ->where('playlist_id', $playlist->id)
                    ->where('property_id', $this->sp_property_id)
                    ->delete();
            }

            //sync to solr
            event(new PlaylistWhitelistUpdatedEvent($playlist));
        }
    }

    public function viewed()
    {
        if ($this->show_new_mark) {
            $this->show_new_mark = false;
            $this->save();
        }

        return true;
    }

    public function delete()
    {
        $playlistTods = DB::table('playlist_terms_of_distribution')->where('tod_id', $this->id)
            ->update(['deleted_at' => Carbon::now()]);
        $this->regionRights()->delete();

        return parent::delete();
    }

    public function getPlaylistCount()
    {
        if (0 == $this->sp_property_id) {
            return count($this->contentProvider->playlists);
        }

        return count($this->playlists);
    }
}
