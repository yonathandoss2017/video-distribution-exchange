<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use App\Events\PropertyCreatedEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    const TYPE_SP_PLUS = 'sp_plus';
    const TYPE_SP = 'sp';
    const TYPE_CP = 'cp';
    const SUB_TYPE_WP = 'wp';
    const SUB_TYPE_EMBED = 'embed';
    const SUB_TYPE_MOBILE = 'mobile';
    const SUB_TYPE_LITE = 'lite';

    const ALLOW = 1;
    const NOT_ALLOW = 0;
    const FEATURE_ALLOWED = 1;
    const FEATURE_NOT_ALLOWED = 0;
    const FEATURE_FORBIDDEN = -1;

    const ID_FOR_ADMIN = 0;

    //To control the hiding/showing of hotspot editing features
    const ID_INVALID_SP = 4000;

    // status Property Deletion
    const STATUS_DELETE_PROCESSING = 99;
    const STATUS_DELETE_FAILED = 100;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['organization_id', 'name', 'type'];

    //Attributes need transformed to date
    protected $dates = ['deleted_at'];

    //Hide certain attributes from response
    protected $hidden = ['api_key', 'api_token', 'pivot'];

    protected $dispatchesEvents = [
        'created' => PropertyCreatedEvent::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function (Property $property) {
            $property->api_key = md5($property->organization_id.$property->id.$property->created_at);
            $property->api_token = md5(Hash::make(str_random(16)));
            $property->uuid = Uuid::uuid4();
            $property->save();
            if (self::TYPE_CP == $property->type) {
                $propertyContent = new PropertyContent([
                    'property_id' => $property->id,
                ]);
                $propertyContent->save();
            }
        });
    }

    public function license_notifications()
    {
        return $this->belongsToMany(User::class, 'license_notifications', 'property_id', 'user_id')
            ->wherePivot('status', true);
    }

    /**
     * Relationship: Property belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'property_id', 'user_id')->whereNotNull('users.activated_at');
    }

    /**
     * Relationship: Property belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'property_id', 'role_id')->withPivot('organization_id', 'user_id');
    }

    /**
     * Relationship: Property(sp plus) has many organizations(provider).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'property_id', 'id');
    }

    /**
     * Relationship: Property(sp/cp) belong to Organization(provider)
     * Relationship: Property(sp plus) belong to Organization(partner).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Property has many metadatas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metadatas()
    {
        return $this->hasMany(PropertyMeta::class, 'property_id', 'id');
    }

    /**
     * Relationship: Property has many analytics monitor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function analytics()
    {
        return $this->hasMany(PropertyAnalytic::class, 'property_id', 'id');
    }

    public function requestLogs()
    {
        return $this->belongsToMany(RequestLog::class, 'request_log_property', 'property_id', 'request_log_id');
    }

    /**
     * Delete property and property related data.
     *
     * @return bool
     */
    public function delete()
    {
        $this->roles()->detach();
        $this->metadatas()->delete();

        return parent::delete();
    }
}
