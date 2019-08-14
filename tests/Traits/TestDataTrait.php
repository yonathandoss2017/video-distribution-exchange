<?php

namespace Tests\Traits;

use App\Models\Role;
use App\Models\User;
use App\Models\Playlist;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use Illuminate\Support\Fluent;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;

trait TestDataTrait
{
    public function createActiveTod(PropertySP $sp)
    {
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
        ]);
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $regionRight = factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => null,
            'ended_at' => null,
        ]);
        $tod->playlists()->attach($playlist);

        return new Fluent([
            'tod' => $tod,
            'regionRight' => $regionRight,
            'playlist' => $playlist,
            'cp' => $cp,
        ]);
    }

    public function createCpAndSpUser()
    {
        $cp = factory(PropertyCP::class)->create();
        $sp = factory(PropertySP::class)->create([
            'organization_id' => $cp->organization_id,
        ]);
        $user = factory(User::class)->create();

        $organizationAdminRole = Role::where('name', Role::ORGANIZATION_ADMIN)->first();

        $user->roles()->attach($organizationAdminRole->id, [
            'organization_id' => $cp->organization_id,
            'property_id' => $cp->id,
        ]);
        $user->roles()->attach($organizationAdminRole->id, [
            'organization_id' => $sp->organization_id,
            'property_id' => $sp->id,
        ]);

        return new Fluent([
            'cp' => $cp,
            'user' => $user,
            'sp' => $sp,
        ]);
    }
}
