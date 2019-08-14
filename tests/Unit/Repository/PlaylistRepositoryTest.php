<?php

namespace Tests\Unit\Repository;

use TestBaseSeeder;
use Tests\TestCase;
use App\Models\Playlist;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use Tests\Traits\TestDataTrait;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;
use App\Repositories\PlaylistRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use TestDataTrait;

    public function setUp()
    {
        parent::setUp();
        $this->seed(TestBaseSeeder::class);
    }

    /** @test */
    public function get_service_providers_external_tod_with_available()
    {
        $sp = factory(PropertySP::class)->create();
        $data = $this->createActiveTod($sp);
        $playlist = $data->playlist;

        $this->assertEquals(1, app(PlaylistRepository::class)->getServiceProviders($playlist)->count());
    }

    /** @test */
    public function get_service_providers_external_tod_without_availability()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();

        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $tod->playlists()->attach($playlist);
        $this->assertEquals(0, app(PlaylistRepository::class)->getServiceProviders($playlist)->count());
    }

    /** @test */
    public function get_service_providers_internal_tod_with_available()
    {
        $sp = factory(PropertySP::class)->create();
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
        ]);
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => 0,
            'cp_property_id' => $cp->id,
            'cp_organization_id' => $sp->organization_id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $regionRight = factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => null,
            'ended_at' => null,
        ]);
        $this->assertEquals(1, app(PlaylistRepository::class)->getServiceProviders($playlist)->count());
    }

    /** @test */
    public function get_service_providers_internal_tod_without_available()
    {
        $sp = factory(PropertySP::class)->create();
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
        ]);
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => 0,
            'cp_property_id' => $cp->id,
            'cp_organization_id' => $sp->organization_id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $tod->playlists()->attach($playlist);
        $this->assertEquals(0, app(PlaylistRepository::class)->getServiceProviders($playlist)->count());
    }

    /** @test */
    public function get_service_providers_external_and_internal_tod_with_available()
    {
        $sp = factory(PropertySP::class)->create();
        $data = $this->createActiveTod($sp);
        $playlist = $data->playlist;

        $sp2 = factory(PropertySP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);

        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => 0,
            'cp_property_id' => $data->cp->id,
            'cp_organization_id' => $sp->organization_id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $regionRight = factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => null,
            'ended_at' => null,
        ]);

        $this->assertEquals(2, app(PlaylistRepository::class)->getServiceProviders($playlist)->count());
    }
}
