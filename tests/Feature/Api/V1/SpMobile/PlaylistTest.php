<?php

namespace Tests\Feature\Api\V1\SpMobile;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\PlatformAlivod;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sp_get_list_playlist_not_pass_key()
    {
        $this->json('get', 'api/v1/sp-mobile/playlists')
            ->assertStatus(401);
    }

    /** @test */
    public function sp_get_list_playlist_failed_credential()
    {
        $params = http_build_query([
            'key' => 1234,
            'token' => 1234,
        ]);
        $this->json('get', 'api/v1/sp-mobile/playlists?'.$params)
            ->assertStatus(403);
    }

    /** @test */
    public function sp_get_list_playlist_success_external_whitelist()
    {
        $sp = factory(PropertySP::class)->create([
            'allow_ivideomobile' => Property::FEATURE_ALLOWED,
        ]);
        $playlist = factory(Playlist::class)->create([
            'status' => Playlist::STATUS_READY,
        ]);
        $termsOfDistribution = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $playlist->tods()->attach($termsOfDistribution->id);
        $distributionRight = factory(DistributionRegionRight::class)->create([
            'tod_id' => $termsOfDistribution->id,
            'started_at' => Carbon::now()->startOfMonth()->toDateTimeString(),
            'ended_at' => Carbon::now(),
        ]);
        $params = http_build_query([
            'key' => $sp->api_key,
            'token' => $sp->api_token,
        ]);
        $this->json('get', 'api/v1/sp-mobile/playlists?'.$params)
            ->assertJsonStructure([
                'status', 'playlists' => ['current_page', 'data' => [
                    ['id', 'name', 'created_at', 'updated_at', 'thumbnail_url', 'thumbnail_path', 'entries_count'],
                ], 'first_page_url', 'from', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to'],
            ])
            ->assertStatus(200);
    }

    /** @test */
    public function sp_get_list_playlist_success_internal_whitelist()
    {
        $property = factory(Property::class)->create([
            'id' => Property::ID_FOR_ADMIN,
        ]);
        $property->id = Property::ID_FOR_ADMIN;
        $property->save();
        $sp = factory(PropertySP::class)->create([
            'allow_ivideomobile' => Property::FEATURE_ALLOWED,
        ]);
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
            'status' => Playlist::STATUS_READY,
        ]);
        $tod = factory(TermsOfDistribution::class)->create([
            'cp_property_id' => $cp->id,
            'sp_property_id' => 0,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $tod->serviceProviders()->attach($sp->id);
        $params = http_build_query([
            'key' => $sp->api_key,
            'token' => $sp->api_token,
        ]);
        $this->json('get', 'api/v1/sp-mobile/playlists?'.$params)
            ->assertJsonStructure([
                'status', 'playlists' => ['current_page', 'data' => [
                    ['id', 'name', 'created_at', 'updated_at', 'thumbnail_url', 'thumbnail_path', 'entries_count'],
                ], 'first_page_url', 'from', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to'],
            ])
            ->assertStatus(200);
    }

    /** @test */
    public function sp_get_playlist_with_entries_external_whitelist()
    {
        $sp = factory(PropertySP::class)->create([
            'allow_ivideomobile' => Property::FEATURE_ALLOWED,
        ]);
        $playlist = factory(Playlist::class)->create([
            'status' => Playlist::STATUS_READY,
        ]);
        $termsOfDistribution = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $playlist->tods()->attach($termsOfDistribution->id);
        $distributionRight = factory(DistributionRegionRight::class)->create([
            'tod_id' => $termsOfDistribution->id,
            'started_at' => Carbon::now()->startOfMonth()->toDateTimeString(),
            'ended_at' => Carbon::now(),
        ]);
        $this->json(
            'GET',
            'api/v1/sp-mobile/playlist/'.$playlist->id.'/videos',
            [],
            $this->header($sp->api_key, $sp->api_token)
        )
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'playlist' => ['id', 'name', 'genre', 'region', 'language', 'property_id', 'entries', 'content_provider'],
            ]);
    }

    /** @test */
    public function sp_get_playlist_with_entries_internal_whitelist()
    {
        $property = factory(Property::class)->create([
            'id' => Property::ID_FOR_ADMIN,
        ]);
        $property->id = Property::ID_FOR_ADMIN;
        $property->save();
        $sp = factory(PropertySP::class)->create([
            'allow_ivideomobile' => Property::FEATURE_ALLOWED,
        ]);
        $cp = factory(PropertyCP::class)->create([
            'organization_id' => $sp->organization_id,
        ]);
        $entry = factory(Entry::class)->create([
            'status' => Entry::STATUS_READY,
            'published' => 1,
        ]);
        factory(PlatformAlivod::class)->create([
            'entry_id' => $entry->id,
        ]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
            'status' => Playlist::STATUS_READY,
        ]);
        $entry->playlists()->attach($playlist->id);
        $tod = factory(TermsOfDistribution::class)->create([
            'cp_property_id' => $cp->id,
            'sp_property_id' => 0,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        $tod->serviceProviders()->attach($sp->id);
        $this->json(
            'GET',
            'api/v1/sp-mobile/playlist/'.$playlist->id.'/videos',
            [],
            $this->header($sp->api_key, $sp->api_token)
        )
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'playlist' => [
                    'id', 'name', 'genre', 'region', 'language', 'property_id',
                    'entries' => [
                        'current_page',
                        'data' => [
                            [
                                'id', 'name', 'description', 'duration', 'views', 'created_at', 'updated_at', 'metadata',
                                'video' => ['id', 'video_id', 'cover_id', 'platform'], 'thumbnail_url',
                            ],
                        ],
                    ],
                    'content_provider',
                ],
            ]);
    }
}
