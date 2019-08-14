<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\PropertyEntryDeletionJob;
use App\Providers\PropertyCPUpdatedEvent;

class PropertyCP extends Property
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'properties';

    protected $dispatchesEvents = [
        'saved' => PropertyCPUpdatedEvent::class,
    ];

    public function __construct(array $attributes = [])
    {
        $attributes['type'] = self::TYPE_CP;
        parent::__construct($attributes);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = parent::newQuery();

        $builder->where('type', '=', self::TYPE_CP);

        return $builder;
    }

    /**
     * Relationship: PropertyCP has many entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class, 'property_id', 'id');
    }

    /**
     * Relationship: PropertyCP has many playlists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'property_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function notifications()
    {
        return $this->hasMany(LicenseNotification::class, 'property_id');
    }

    public function propertyContent()
    {
        if (self::TYPE_CP == $this->type) {
            return $this->hasOne(PropertyContent::class, 'property_id');
        }

        return null;
    }

    public function oauth()
    {
        return $this->hasMany(PlatformOauth::class, 'property_id');
    }

    public function marketplaceTerm()
    {
        return $this->hasOne(MarketplaceTerm::class, 'property_id');
    }

    public function termsOfMarketplaces()
    {
        return $this->hasMany(TermsOfMarketplace::class, 'property_id');
    }

    //DEPRECATED, TO-BE-REMOVED, please use tods() instead
    public function termsOfDistributions()
    {
        return $this->tods();
    }

    public function tods()
    {
        return $this->hasMany(TermsOfDistribution::class, 'cp_property_id', 'id')
            ->whereNull('cp_deleted_at');
    }

    public function todsWithTrashed()
    {
        return $this->hasMany(TermsOfDistribution::class, 'cp_property_id', 'id');
    }

    /**
     * Relationship: PropertyCP has many user_subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'property_id', 'id');
    }

    /**
     * Return TODs that has status active only.
     */
    public function activeTods()
    {
        return $this->tods()
            ->where('status', TermsOfDistribution::STATUS_ACTIVE);
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
                $q->where('started_at', '<=', Carbon::now());
                $q->where('ended_at', '>=', Carbon::now());
            });
        });
    }

    /**
     * Return TOD for automatic internal whitelist, take note that this is single object, not a collections.
     */
    public function internalTod()
    {
        return $this->hasOne(TermsOfDistribution::class, 'cp_property_id', 'id')
        ->whereNull('cp_deleted_at')
            ->where('status', TermsOfDistribution::STATUS_ACTIVE)
            ->where('sp_property_id', 0);
    }

    /**
     * Delete property and property related data.
     *
     * @return bool
     */
    public function delete()
    {
        DB::beginTransaction();
        try {
            $entries = $this->entries()->get();
            if ($entries->count() < 50 && $entries->count() > 0) {
                foreach ($entries as $entry) {
                    $entry->delete();
                }
            } elseif ($entries->count() >= 50) {
                $this->status = Property::STATUS_DELETE_PROCESSING;
                $this->save();
                dispatch(new PropertyEntryDeletionJob($this));

                return true;
            }
            $this->playlists()->get()->map(function ($playlist) {
                $playlist->delete();
            });
            $this->notifications()->delete();
            $this->todsWithTrashed()->delete();
            $this->oauth()->delete();
            $this->propertyContent()->delete();
            $result = parent::delete();
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            \Log::error('Property CP Delete '.$e->getMessage());
            DB::rollback();

            return false;
        }
    }
}
