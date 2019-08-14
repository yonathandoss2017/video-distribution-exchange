<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AdminNotification;
use App\Notifications\UserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserActivation\UserActivationEmail;
use App\Notifications\UserActivation\UserActivationWithPassResetEmail;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    protected $table = 'users';

    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'remarks', 'longid', 'activated_at', 'role_user_backup', 'is_disabled',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'last_active'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Generate a rand string.
     *
     * @param $length
     * @param string $charset
     *
     * @return string
     */
    private static function randString($length, $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+{}|:<>?,.;[]-=')
    {
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }

        return $str;
    }

    /**
     * Generate a secret for api.
     *
     * @return string
     */
    public static function generateSecret()
    {
        return self::randString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * Check if user is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        $query = $this->roles()->where('name', Role::SUPER_ADMIN)->get();

        return 0 != $query->count();
    }

    /**
     * Check if user is current organization's admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (null !== Organization::Organization()) {
            $query = $this->roles()->wherePivot('organization_id', Organization::Organization()->id)
                ->where('name', Role::ORGANIZATION_ADMIN)->get();

            return 0 != $query->count();
        }

        return false;
    }

    /**
     * Check if user is given organization's admin.
     *
     * @return bool
     */
    public function isOrganizationAdmin($organization_id)
    {
        $query = $this->roles()->wherePivot('organization_id', $organization_id)
            ->where('name', Role::ORGANIZATION_ADMIN)->get();

        return 0 != $query->count();
    }

    /**
     * Check if user is super operator.
     *
     * @return bool
     */
    public function isOperator()
    {
        return 0 != $this->roles()->where('name', Role::SUPER_OPERATOR)->count();
    }

    /**
     * Check if user has roles.
     *
     * @return bool
     */
    public function hasRoles($property)
    {
        if (null !== $property) {
            return  0 != $this->roles()->wherePivot('organization_id', $property->organization_id)
                ->wherePivot('property_id', $property->id)
                ->count();
        }

        return false;
    }

    public function isManager($property)
    {
        if (null !== $property) {
            $query = $this->roles()->wherePivot('organization_id', $property->organization_id)
                ->wherePivot('property_id', $property->id)
                ->where('name', Role::PROPERTY_MANAGER)->get();

            return 0 != $query->count();
        }

        return false;
    }

    public function isContentUploader($property)
    {
        if (null !== $property) {
            $query = $this->roles()->wherePivot('organization_id', $property->organization_id)
                ->wherePivot('property_id', $property->id)
                ->where('name', Role::CONTENT_UPLOADER)->get();

            return 0 != $query->count();
        }

        return false;
    }

    public function isCensor($property)
    {
        if (null !== $property) {
            $query = $this->roles()->wherePivot('organization_id', $property->organization_id)
                ->wherePivot('property_id', $property->id)
                ->where('name', Role::CENSOR);

            return 0 != $query->count();
        }

        return false;
    }

    /**
     * Chceck if user is manager of service provider.
     *
     * @return bool
     */
    public function isSpManager($property_id)
    {
        $count = DB::table('users')
            ->join('role_user', function ($join) use ($property_id) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.organization_id', '=', Organization::Organization()->id)
                    ->where('role_user.property_id', '=', $property_id)
                    ->where('users.id', '=', $this->id);
            })
            ->join('roles', function ($join) {
                $join->on('role_user.role_id', '=', 'roles.id')
                    ->where('roles.name', '=', Role::PROPERTY_MANAGER);
            })
            ->join('properties', function ($join) {
                $join->on('role_user.property_id', '=', 'properties.id')
                    ->where('properties.type', '=', Property::TYPE_SP);
            })
            ->count();

        return 0 != $count;
    }

    /**
     * Check whether User only has SP account management rights.
     *
     * @return bool
     */
    public function onlySpManageRight()
    {
        $properties = $this->properties()->get();

        $sps = $properties->filter(function ($property) {
            return Property::TYPE_SP == $property->type;
        });

        return $properties->count() == $sps->count();
    }

    /**
     * Check if user is manager of content provider.
     *
     * @return bool
     */
    public function isCpManager($property_id)
    {
        $count = DB::table('users')
            ->join('role_user', function ($join) use ($property_id) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.organization_id', '=', Organization::Organization()->id)
                    ->where('role_user.property_id', '=', $property_id)
                    ->where('users.id', '=', $this->id);
            })
            ->join('roles', function ($join) {
                $join->on('role_user.role_id', '=', 'roles.id')
                    ->where('roles.name', '=', Role::PROPERTY_MANAGER);
            })
            ->join('properties', function ($join) {
                $join->on('role_user.property_id', '=', 'properties.id')
                    ->where('properties.type', '=', Property::TYPE_CP);
            })
            ->count();

        return 0 != $count;
    }

    /**
     * Check if user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return null != $this->activated_at;
    }

    /**
     * Find or create a user.
     *
     * @param $email
     *
     * @return static
     */
    public static function findOrCreate($email, $name = null)
    {
        $userName = (null == $name ? $email : $name);

        $user = self::where('email', $email)->first();
        if (null === $user) {
            $user = self::create([
                'name' => $userName,
                'email' => $email,
                'password' => Hash::make('ABCabc01'),
                'remarks' => $email,
                'longid' => md5($email),
            ]);
        }

        return $user;
    }

    /**
     * Create token and send active email.
     *
     * @param $message
     * @param $url
     * @param $locale
     */
    public function sendAdminNotification($message, $url, $locale)
    {
        if ($this->isActive()) {
            $this->notify(new AdminNotification($message, $url));

            return;
        }
        $token = Password::broker()->createToken($this);
        //update user without updating any data
        $this->touch();
        //create notifications send email
        $this->token = $token;
        $this->notify(new UserActivationWithPassResetEmail($this, $locale));
    }

    /**
     * Send verify email.
     *
     * @param $locale
     */
    public function sendVerifyEmail($locale)
    {
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $user_activates = DB::table('user_activations')->where('email', $this->email)->first();
        if (!$user_activates) {
            DB::table('user_activations')->insert(['email' => $this->email, 'token' => $token, 'created_at' => new Carbon()]);
        } else {
            DB::table('user_activations')->where('email', $this->email)->update(['token' => $token, 'created_at' => new Carbon()]);
        }
        //create notifications send email
        $this->token = $token;
        $this->notify(new UserActivationEmail($this, $locale));
    }

    /**
     * Rewrite reset password email.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token, $locale = null)
    {
        if (!$locale) {
            $locale = App::getLocale();
        }
        $this->notify(new UserResetPassword($token, $this->email, $this->name, $locale));
    }

    /**
     * User belong to many Organizations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'role_user', 'user_id', 'organization_id');
    }

    /**
     * User belong to many Properties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'role_user', 'user_id', 'property_id');
    }

    /**
     * User belong to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withPivot('organization_id', 'property_id');
    }

    /**
     * User has many entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function licenseNotifications()
    {
        return $this->hasMany(LicenseNotification::class);
    }

    /**
     * Get all super admins.
     *
     * @return mixed
     */
    public static function superAdmins()
    {
        return self::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', Role::SUPER_ADMIN)->select('users.*')->get();
    }

    /**
     * Get all service provider user can manage.
     *
     * @return mixed
     */
    public function serviceProviderCanManage()
    {
        if ($this->isSuperAdmin()) {
            return PropertySP::join('organizations', 'properties.organization_id', 'organizations.id')
                ->select('properties.*', 'organizations.name as organization_name')->orderBy('organization_name')->get();
        }

        $admin_id = -1;
        $manager_id = -1;
        $roles = Role::where('name', Role::ORGANIZATION_ADMIN)->orWhere('name', Role::PROPERTY_MANAGER)->get();
        foreach ($roles as $role) {
            if (Role::ORGANIZATION_ADMIN == $role->name) {
                $admin_id = $role->id;
            } else {
                $manager_id = $role->id;
            }
        }

        if (-1 == $admin_id || -1 == $manager_id) {
            return null;
        }

        return PropertySP::with('organization')
            ->join('organizations', 'properties.organization_id', 'organizations.id')
            ->join('role_user', function ($join) use ($admin_id, $manager_id) {
                $join->on('role_user.organization_id', '=', 'organizations.id')
                    ->where('role_user.role_id', $admin_id)
                    ->orOn('role_user.property_id', '=', 'properties.id')
                    ->where('role_user.role_id', $manager_id);
            })
            ->where('role_user.user_id', $this->id)
            ->select('properties.*', 'organizations.name as organization_name')->distinct()->get();
    }

    public function findUserByEmail($email)
    {
        return self::where('email', $email)
            ->first();
    }

    public function isAcceptNotificationForProperty($property_id)
    {
        $propertyNotification = $this->licenseNotifications->where('property_id', $property_id)->first();

        return $propertyNotification && optional($propertyNotification)->status;
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
}
