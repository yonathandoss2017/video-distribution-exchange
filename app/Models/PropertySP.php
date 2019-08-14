<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class PropertySP extends Property
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'properties';

    public function __construct(array $attributes = [])
    {
        $attributes['type'] = self::TYPE_SP;
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

        $builder->where('properties.type', '=', self::TYPE_SP);

        return $builder;
    }

    /**
     * Get all entries of ServiceProvider based on active whitelist playlists.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function entries($status = null, $playlist_id = null, $search = null)
    {
        $property_id = $this->id;

        // 1. Get entries that have playlists active tod
        return Entry::whereHas('playlists', function ($q) use ($property_id, $playlist_id) {
            if ($playlist_id) {
                $q->where('id', $playlist_id);
            } else {
                $q->whereIn('id', Playlist::whitelistedForSP($this)->pluck('id')->all());
            }
        })

        // 2. If there are given status, filter entries based on status
        ->when($status, function ($q) use ($status) {
            switch ($status) {
                case Entry::STATUS_READY:
                    $q->where('entries.status', Entry::STATUS_READY);
                    break;
            }
        })

        // 3. Filter entries based on search
        ->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })

        //4. Filter not approved and error entries
        ->whereNotIn('status', Entry::not_ready_status())
        ->distinct()
        ->select('entries.*');
    }

    public function playlistProperty()
    {
        //DO NOT USE, use PlaylistProperty::findRecord() instead!
        /*return $this->belongsToMany(Playlist::class, 'playlist_property', 'property_id', 'playlist_id')
            ->withPivot('playlist_name')
            ->withTimestamps();*/
    }

    /**
     * Relationship: ServiceProvider has one site.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site()
    {
        return $this->hasOne(Site::class, 'property_id', 'id');
    }

    /**
     * Relationship: Property has many entry analytics monitor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entry_analytics()
    {
        return $this->hasMany(EntryAnalytic::class, 'property_id');
    }

    /**
     * Relationship: ServiceProvider belongsToMany entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entry_property()
    {
        return $this->belongsToMany(Entry::class, 'entry_property', 'property_id', 'entry_id');
    }

    public function tods()
    {
        return $this->hasMany(TermsOfDistribution::class, 'sp_property_id', 'id')
            ->whereNull('sp_deleted_at');
    }

    public function todsWithTrashed()
    {
        return $this->hasMany(TermsOfDistribution::class, 'sp_property_id', 'id');
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
            $this->entry_analytics()->delete();
            $this->entry_property()->detach();
            PlaylistProperty::deleteRecordsByServiceProvider($this);
            $this->analytics()->delete();
            $this->todsWithTrashed()->delete();
            $result = parent::delete();
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            \Log::error('Property SP Delete '.$e->getMessage());
            DB::rollback();

            return false;
        }
    }
}
