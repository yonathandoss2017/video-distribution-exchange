<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\Organization;
use App\Models\OrganizationFeature;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('super-admin', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('organization-admin', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->isOrganizationAdmin(optional(Organization::Organization())->id);
        });

        Gate::define('dpp-admin', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->roles()->where('name', Role::DPP_ADMIN)->count() > 0;
        });

        Gate::define('edit-organization', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('edit-provider', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->isAdmin();
        });

        Gate::define('manage-provider', function ($user, $property) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            //super admin can
            if ($user->isAdmin()) {
                return true;
            }
            //organization admin can
            return $user->isManager($property); //property manager can
        });

        Gate::define('edit-user', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->isAdmin();
        });

        Gate::define('manage-property', function ($user, $property) {
            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }

            return $this->baseUserPermissionCheck($user, $property);
        });

        Gate::define('manager-cp-playlist', function ($user, $property, $playlist) {
            if (null == $playlist) {
                return false;
            }
            if (is_object($property)) {
                $property = $property->id;
            }
            if (!is_object($playlist)) {
                $playlist = Playlist::findOrFail($playlist);
            }
            if ($playlist->property_id != $property) {
                return false;
            }

            return true;
        });

        Gate::define('create-cp-playlist', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            return $this->userLevelCheck(Role::CONTENT_UPLOADER, $user, $property);
        });

        Gate::define('manager-sp-playlist', function ($user, $property, $playlist) {
            if (null == $playlist) {
                return false;
            }
            if (is_object($property)) {
                $property = PropertySP::findOrFail($property);
            }
            if (!is_object($playlist)) {
                $playlist = Playlist::findOrFail($playlist);
            }

            $playlists = Playlist::whitelistedForSP($property)->get();
            if (!$playlists->contains($playlist)) {
                return false;
            }

            return true;
        });

        Gate::define('manage-exchange', function ($user, $property) {
            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }

            return $this->userLevelCheck(Role::PROPERTY_MANAGER, $user, $property);
        });

        Gate::define('manage-cp-request-log-comments', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            if (!config('features.content_review')) {
                return false;
            }

            return $this->userLevelCheck(Role::CENSOR, $user, $property);
        });

        Gate::define('manage-cp-request-logs', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            if (!config('features.content_review')) {
                return false;
            }

            return $this->userLevelCheck(Role::CONTENT_UPLOADER, $user, $property) || $this->userLevelCheck(Role::CENSOR, $user, $property);
        });

        Gate::define('upload-video', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            return $this->userLevelCheck(Role::CONTENT_UPLOADER, $user, $property);
        });

        Gate::define('manage-dpp', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            return $this->userLevelCheck(Role::PROPERTY_MANAGER, $user, $property);
        });

        Gate::define('manage-block-chain', function ($user, $property) {
            if (!is_object($property)) {
                $property = PropertyCP::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            return $this->userLevelCheck(Role::PROPERTY_MANAGER, $user, $property);
        });

        Gate::define('manage-setting', function ($user, $property) {
            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }

            return $this->userLevelCheck(Role::PROPERTY_MANAGER, $user, $property);
        });

        Gate::define('manage-ai-review', function ($user, $property) {
            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            $hasFeature = $property->organization->hasFeature(OrganizationFeature::FEATURE_AI_REVIEW);
            if (!$hasFeature[OrganizationFeature::FEATURE_AI_REVIEW]) {
                return false;
            }

            return $this->userLevelCheck(Role::CENSOR, $user, $property);
        });

        Gate::define('view-ai-review', function ($user, $property) {
            if (!is_object($property)) {
                $property = Property::findOrFail($property);
            }
            if (null == $property) {
                return false;
            }
            if (Property::TYPE_CP != $property->type) {
                return false;
            }

            $hasFeature = $property->organization->hasFeature(OrganizationFeature::FEATURE_AI_REVIEW);
            if (!$hasFeature[OrganizationFeature::FEATURE_AI_REVIEW]) {
                return false;
            }

            return $this->userLevelCheck(Role::CONTENT_UPLOADER, $user, $property) || $this->userLevelCheck(Role::CENSOR, $user, $property);
        });
    }

    private function baseUserPermissionCheck($user, $property)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if (Property::TYPE_CP == $property->type && $user->isOperator()) {
            return true;
        }
        if ($user->isOrganizationAdmin($property->organization_id)) {
            return true;
        }
        if ($user->hasRoles($property)) {
            return true;
        }

        return false;
    }

    private function userLevelCheck($level, $user, $property)
    {
        if (Role::SUPER_ADMIN == $level) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        } elseif (Role::ORGANIZATION_ADMIN == $level) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->isOrganizationAdmin($property->organization_id)) {
                return true;
            }
        } elseif (Role::PROPERTY_MANAGER == $level) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->isOrganizationAdmin($property->organization_id)) {
                return true;
            }
            if ($user->isManager($property)) {
                return true;
            }
        } elseif (Role::CONTENT_UPLOADER == $level) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->isOrganizationAdmin($property->organization_id)) {
                return true;
            }
            if ($user->isManager($property)) {
                return true;
            }
            if ($user->isContentUploader($property)) {
                return true;
            }
        } elseif (Role::CENSOR == $level) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->isOrganizationAdmin($property->organization_id)) {
                return true;
            }
            if ($user->isManager($property)) {
                return true;
            }
            if ($user->isCensor($property)) {
                return true;
            }
        }

        return false;
    }
}
