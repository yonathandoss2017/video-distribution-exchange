<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\OrganizationEntryDeletionJob;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    const ID_FOR_SUPER_ADMIN = 0;

    // status Organization Deletion
    const STATUS_DELETE_PROCESSING = 99;
    const STATUS_DELETE_FAILED = 100;

    private static $organization = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'address', 'country'];

    public static function setCurrentOrganization($organization)
    {
        if (!is_object($organization)) {
            self::$organization = self::find($organization);
        } else {
            self::$organization = $organization;
        }
    }

    public static function Organization()
    {
        return self::$organization;
    }

    public function getDiskSpaceAttribute()
    {
        $size = (float) ($this->storage_size_in_byte / (1024 * 1024 * 1024));
        if ($size >= 1) {
            return round($size, 2).' GB';
        } else {
            $size = (float) ($this->storage_size_in_byte / (1024 * 1024));
            if ($size >= 1) {
                return round($size, 2).' MB';
            } else {
                return round((float) ($this->storage_size_in_byte / 1024), 2).' KB';
            }
        }
    }

    /**
     * TODO: looking for where this relationship used and remove additional query after not used
     * Organization belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'organization_id', 'user_id')->whereNotNull('users.activated_at');
    }

    /**
     * Get all user related to Organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allUsers()
    {
        return $this->belongsToMany(User::class, 'role_user', 'organization_id', 'user_id');
    }

    /**
     * Organization belongs to many admins.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        $role = Role::where('name', Role::ORGANIZATION_ADMIN)->first();

        return $this->users()->wherePivot('organization_id', $this->id)->wherePivot('role_id', $role->id);
    }

    /**
     * Organization has many properties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Organization has many ServiceProviderProperties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_providers()
    {
        return $this->hasMany(PropertySP::class);
    }

    /**
     * Relationship: Organization has many ContentProviderProperties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content_providers()
    {
        return $this->hasMany(PropertyCP::class);
    }

    /**
     * Organization has many playlist through property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function playlists()
    {
        return $this->hasManyThrough(Playlist::class, Property::class);
    }

    /**
     * Organization has many entries through property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function entries()
    {
        return $this->hasManyThrough(Entry::class, Property::class);
    }

    /**
     * Organization has feature setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feature()
    {
        return $this->hasOne(OrganizationFeature::class);
    }

    /**
     * check organization feature whether enabled.
     *
     * @param $features array
     *
     * @return bool
     */
    public function hasFeature($features)
    {
        return optional($this->feature)->has($features);
    }

    /**
     * Delete organization and organization related data.
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
                $this->status = self::STATUS_DELETE_PROCESSING;
                $this->save();
                dispatch(new OrganizationEntryDeletionJob($this));

                DB::commit();

                return true;
            }
            $this->content_providers()->delete();
            $this->service_providers()->delete();

            $result = parent::delete();
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            \Log::error('Organization Delete '.$e->getMessage());
            DB::rollback();

            return false;
        }
    }
}
