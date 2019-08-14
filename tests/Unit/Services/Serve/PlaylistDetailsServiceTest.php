<?php

namespace Tests\Unit\Serve;

use Tests\TestCase;
use App\Models\User;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertySP;
use Faker\Factory as Faker;
use App\Models\Organization;
use App\Models\PlaylistProperty;
use App\Services\Serve\PlaylistDetailsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistDetailsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        // Seed Data
        $this->org = factory(Organization::class)->create();
        $this->user = factory(User::class)->create();
        $this->cp = factory(Property::class)->states('cp')->create(['organization_id' => $this->org->id]);
        $this->invalid_sp = rand(1000, 10000);
        $this->faker = Faker::create();
    }

    protected function createPlaylist($extra_data = [])
    {
        $base_data = ['property_id' => $this->cp->id];
        $base_data = array_merge($base_data, $extra_data);

        return factory(Playlist::class)->create($base_data);
    }

    public function test_get_name_playlist_with_cp_id()
    {
        $playlist = $this->createPlaylist();

        $actual_name = PlaylistDetailsService::getName($playlist, $playlist->property_id);
        $expected_name = $playlist->name;

        $this->assertEquals($actual_name, $expected_name);
    }

    public function test_get_name_playlist_with_invalid_cp_id()
    {
        $property = factory(Property::class)->create([
            'id' => $this->invalid_sp,
        ]);

        $playlist = $this->createPlaylist(['property_id' => $this->invalid_sp]);

        $actual_name = PlaylistDetailsService::getName($playlist, $playlist->property_id);
        $expected_name = $playlist->name;

        $this->assertEquals($actual_name, $expected_name);
    }

    protected function createSP($extra_data = [])
    {
        $base_data = ['organization_id' => $this->org->id];
        $base_data = array_merge($base_data, $extra_data);

        return factory(PropertySP::class)->create($base_data);
    }

    protected function attachSP($playlist, $sp, $extra_data = [])
    {
        PlaylistProperty::createOrUpdateRecord($playlist->property_id, $sp->id, $playlist->id, $extra_data);
    }

    protected function createAndAttachSp($playlist, $extra_data = [])
    {
        $sp = $this->createSP();
        $this->attachSP($playlist, $sp, $extra_data);

        return $sp;
    }

    public function test_get_name_playlist_with_sp_id()
    {
        $playlist = $this->createPlaylist();
        $sp = $this->createAndAttachSp($playlist, [
                    'playlist_name' => $playlist->name,
                ]);

        $actual_name = PlaylistDetailsService::getName($playlist, $sp->id);
        $expected_name = PlaylistProperty::findRecord($playlist->property_id, $sp->id, $playlist->id)->first()->playlist_name;

        $this->assertEquals($actual_name, $expected_name);
    }

    public function test_get_name_playlist_with_sp_id_custom_playlist_name()
    {
        $new_playlist_name = $this->faker->sentence();
        $playlist = $this->createPlaylist();
        $sp = $this->createAndAttachSp($playlist, [
                    'playlist_name' => $new_playlist_name,
                ]);
        $actual_name = PlaylistDetailsService::getName($playlist, $sp->id);

        $this->assertEquals($actual_name, $new_playlist_name);
    }

    public function test_get_name_playlist_with_sp_id_but_without_sp_playlist_name()
    {
        $playlist = $this->createPlaylist();
        $sp = $this->createAndAttachSp($playlist);

        $actual_name = PlaylistDetailsService::getName($playlist, $sp->id);
        $expected_name = $playlist->name;

        $this->assertEquals($actual_name, $expected_name);
    }

    public function test_get_name_playlist_with_invalid_sp_id()
    {
        $playlist = $this->createPlaylist();
        $sp = $this->createAndAttachSp($playlist);

        $actual_name = PlaylistDetailsService::getName($playlist, $this->invalid_sp);
        $expected_name = $playlist->name;

        $this->assertEquals($actual_name, $expected_name);
    }
}
