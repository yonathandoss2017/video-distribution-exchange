<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const SUPER_ADMIN = 'super_admin';
    const SUPER_OPERATOR = 'super_operator';
    const ORGANIZATION_ADMIN = 'organization_admin';
    const PROPERTY_MANAGER = 'property_manager';
    const CONTENT_EDITOR = 'content_editor';
    const CONTENT_CREATOR = 'content_creator';
    const CONTENT_UPLOADER = 'content_uploader';
    const DPP_ADMIN = 'dpp_admin';
    const CENSOR = 'censor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    public static function getRoles($type = 'all')
    {
        if ('all' == $type) {
            $query = self::all();
        } else {
            $roles = [];
            if (Property::TYPE_CP == $type) {
                $roles = [self::PROPERTY_MANAGER, self::CONTENT_UPLOADER, self::CENSOR];
            } elseif (Property::TYPE_SP == $type) {
                $roles = [self::PROPERTY_MANAGER];
            }
            $query = self::whereIn('name', $roles)->get();
        }
        $roles = ['0' => __('common.n_a')];
        foreach ($query as $item) {
            $roles[$item->id] = __('common.'.$item->name);
        }

        return $roles;
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('organization_id', 'property_id');
    }

    /**
     * Role belongs to many properties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'role_user', 'role_id', 'property_id')->withPivot('organization_id', 'user_id');
    }
}
