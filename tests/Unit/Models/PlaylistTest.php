<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Models\PlaylistProperty;
use App\Models\TermsOfDistribution;
use Illuminate\Support\Facades\Bus;
use App\Events\PlaylistUpdatingEvent;
use Illuminate\Support\Facades\Event;
use App\Models\DistributionRegionRight;
use App\Repositories\CountryRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\PlaylistRepository;
use App\Repositories\ContentProviderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\Solr\SyncPlaylistEntriesToSolrMarketplace;

class PlaylistTest extends TestCase
{
    use RefreshDatabase;

    private $contentProviderRepository;
    private $countryRepository;
    private $languageRepository;
    private $playlistRepository;
    private $formRequest;

    public function setUp()
    {
        $this->contentProviderRepository = $this->prophesize(ContentProviderRepository::class);
        $this->countryRepository = $this->prophesize(CountryRepository::class);
        $this->languageRepository = $this->prophesize(LanguageRepository::class);
        $this->playlistRepository = $this->prophesize(PlaylistRepository::class);
        $this->formRequest = $this->prophesize(Request::class);
        parent::setUp();
    }

    /** @test */
    public function playlist_updating_trigger_playlist_updating_event()
    {
        Event::fake();
        $playlist = factory(Playlist::class)->create();
        $playlist->update(['genre' => 'news']);

        Event::assertDispatched(PlaylistUpdatingEvent::class);
    }

    /** @test */
    public function playlist_updating_genre_changed()
    {
        $entry = factory(Entry::class)->create();
        Bus::fake();
        $playlist = factory(Playlist::class)->create();
        $playlist->entries()->attach($entry->id);
        $playlist->update(['genre' => 'news']);

        Bus::assertDispatched(SyncPlaylistEntriesToSolrMarketplace::class, function ($job) use ($playlist) {
            return $job->playlist->id === $playlist->id;
        });
    }

    /** @test */
    public function playlist_updating_genre_not_changed()
    {
        $entry = factory(Entry::class)->create();
        $playlist = factory(Playlist::class)->create(['genre' => 'sport']);
        $playlist->entries()->attach($entry->id);

        Bus::fake();

        $playlist->update(['name' => 'news']);

        Bus::assertNotDispatched(SyncPlaylistEntriesToSolrMarketplace::class, function ($job) use ($playlist) {
            return $job->playlist->id === $playlist->id;
        });
    }

    /** @test */
    public function playlist_published()
    {
        $playlist = factory(Playlist::class)->create(['publish_status' => Playlist::PUBLISH_STATUS_UNPUBLISH]);

        Bus::fake();
        $playlist->publish_status = Playlist::PUBLISH_STATUS_PUBLISHED;
        $playlist->save();
        Bus::assertDispatched(SyncPlaylistEntriesToSolrMarketplace::class, function ($job) use ($playlist) {
            return $job->playlist->id === $playlist->id;
        });
    }

    /** @test */
    public function load_entries()
    {
        $playlist = factory(Playlist::class)->create();
        $entry1 = factory(Entry::class)->create();
        $entry2 = factory(Entry::class)->create();
        $entry3 = factory(Entry::class)->create();
        $playlist->entries()->attach($entry1->id);
        $playlist->entries()->attach($entry2->id);
        $playlist->entries()->attach($entry3->id);

        $playlist->load('entries');

        $this->assertEquals(3, $playlist->entries->count());

        $playlist->fresh();

        $playlist->loadEntries(2);

        $this->assertEquals(2, $playlist->entries->count());

        $playlist->fresh();

        $playlist->loadEntries(2, $entry2->id);

        $this->assertEquals(1, $playlist->entries->count());

        $this->assertEquals($entry3->id, $playlist->entries->first()->id);
    }

    /** @test */
    public function playlist_has_tod_active_and_availability_period_null()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => null,
            'ended_at' => null,
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::ExternalWhitelisted($sp, true)
            ->get();

        $this->assertEquals(1, count($playlists));
    }

    /** @test */
    public function playlist_has_tod_active_and_availability_period_not_null()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subDay()->toDateTimeString(),
            'ended_at' => Carbon::now()->addDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::ExternalWhitelisted($sp, true)
            ->get();

        $this->assertEquals(1, count($playlists));
    }

    /** @test */
    public function playlist_has_tod_active_and_availability_period_not_in_period()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subMonth()->toDateTimeString(),
            'ended_at' => Carbon::now()->subDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::ExternalWhitelisted($sp, true)
            ->get();

        $this->assertEquals(0, count($playlists));
    }

    /** @test */
    public function test_scope_published()
    {
        $playlist = factory(Playlist::class)->create();
        $this->assertNull(Playlist::published()->where('id', $playlist->id)->first());

        $playlist = factory(Playlist::class)->create(['publish_status' => Playlist::PUBLISH_STATUS_PUBLISHED]);
        $this->assertNotNull(Playlist::published()->where('id', $playlist->id)->first());
    }

    /** @test */
    public function test_check_license_status()
    {
        $playlist = factory(Playlist::class)->create();
        $playlist_property = factory(PlaylistProperty::class)->create(['property_id' => $playlist->property_id, 'playlist_id' => $playlist->id]);
        $this->assertNull($playlist->checkLicenseStatus($playlist->property_id));

        $playlist = factory(Playlist::class)->create();
        $playlist_property = factory(PlaylistProperty::class)->create();
        $this->assertEquals('N/A', $playlist->checkLicenseStatus($playlist->property_id));
    }

    /** @test */
    public function test_get_sp_playlist_name()
    {
        $playlist = factory(Playlist::class)->create();
        $playlist_property = factory(PlaylistProperty::class)->create(['property_id' => $playlist->property_id, 'playlist_id' => $playlist->id]);
        $this->assertNotNull($playlist->getSpPlaylistName($playlist->property_id));

        $playlist = factory(Playlist::class)->create();
        $playlist_property = factory(PlaylistProperty::class)->create();
        $this->assertNull($playlist->getSpPlaylistName($playlist->property_id));
    }

    /** @test */
    public function test_formatted_publish_start_date()
    {
        $playlist = factory(Playlist::class)->create();
        $this->assertNull($playlist->formattedStartDate());

        $playlist = factory(Playlist::class)->create(['publish_start_date' => date('Y-m-d H:i:s')]);
        $this->assertNotNull($playlist->formattedStartDate());
    }

    /** @test */
    public function test_formatted_publish_end_date()
    {
        $playlist = factory(Playlist::class)->create();
        $this->assertNull($playlist->formattedEndDate());

        $playlist = factory(Playlist::class)->create(['publish_end_date' => date('Y-m-d H:i:s')]);
        $this->assertNotNull($playlist->formattedEndDate());
    }

    /** @test */
    public function test_terms_of_distribution_service_providers_count()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create(['property_id' => $sp->id]);
        $playlist_property = factory(PlaylistProperty::class)->create(['property_id' => $playlist->property_id, 'playlist_id' => $playlist->id]);
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subMonth()->toDateTimeString(),
            'ended_at' => Carbon::now()->subDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $this->assertEquals(0, $playlist->termsOfDistributionServiceProvidersCount());
    }

    /** @test */
    public function test_delete()
    {
        $playlist = factory(Playlist::class)->create();
        $this->assertTrue($playlist->delete());
    }

    /** @test */
    public function test_get_active_tod_by_sp()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $this->assertNull($playlist->getActiveTodBySp($sp->id));
    }

    /** @test */
    public function test_scope_whitelisted_for_sp_with_availability()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subMonth()->toDateTimeString(),
            'ended_at' => Carbon::now()->subDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::WhitelistedForSP($sp)
            ->get();

        $this->assertEquals(0, count($playlists));
    }

    /** @test */
    public function test_scope_internal_whitelisted()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subMonth()->toDateTimeString(),
            'ended_at' => Carbon::now()->subDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::InternalWhitelisted($sp)
            ->get();

        $this->assertEquals(0, count($playlists));
    }

    /** @test */
    public function test_scope_external_whitelisted()
    {
        $sp = factory(PropertySP::class)->create();
        $playlist = factory(Playlist::class)->create();
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $sp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->subMonth()->toDateTimeString(),
            'ended_at' => Carbon::now()->subDay()->toDateTimeString(),
        ]);
        $playlist->tods()->attach($tod->id);
        $playlists = Playlist::ExternalWhitelisted($sp, false)
            ->get();

        $this->assertEquals(1, count($playlists));
    }
}
