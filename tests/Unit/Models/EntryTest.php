<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Faker\Factory;
use Tests\TestCase;
use App\Models\User;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\EntryMeta;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\EntryAnalytic;
use App\Models\EntrySubtitle;
use App\Models\PlatformAlivod;
use App\Models\EntryLocalization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_valid_platform()
    {
        $this->assertFalse(Entry::isValidPlatform('test'));

        foreach (Entry::PLATFORMS as $PLATFORM) {
            $this->assertTrue(Entry::isValidPlatform($PLATFORM));
        }
    }

    /**
     * @test
     */
    public function test_scope_published()
    {
        $entry1 = factory(Entry::class)->create();
        $entry2 = factory(Entry::class)->create(['published_at' => null]);

        $this->assertNotNull(Entry::published()->where('id', $entry1->id)->first());
        $this->assertNotNull(Entry::published()->where('id', $entry2->id)->first());
    }

    /**
     * @test
     */
    public function test_scope_not_published()
    {
        $entry = factory(Entry::class)->create([
            'published_at' => Carbon::now()->addHour(),
        ]);

        $this->assertNotNull(Entry::notPublished()->where('id', $entry->id)->first());
    }

    /**
     * @test
     */
    public function test_get_entry_is_pending()
    {
        $entry = factory(Entry::class)->create([
            'published_at' => Carbon::now()->addHour(),
        ]);

        $this->assertTrue($entry->isPending);
    }

    /**
     * @test
     */
    public function test_scope_with_platform_videos()
    {
        $entry = factory(Entry::class)->create();
        $platform_alivod = factory(PlatformAlivod::class)->create([
            'entry_id' => $entry->id,
            'status' => PlatformAlivod::STATUS_READY,
        ]);

        Entry::withPlatformVideos()->get()->map(function ($video) use ($entry, $platform_alivod) {
            $this->assertEquals($platform_alivod->id, $video->platformAlivod->id);
            $this->assertEquals($entry->id, $video->id);
        });
    }

    /**
     * @test
     */
    public function test_scope_where_has_platform_videos()
    {
        $entry = factory(Entry::class)->create();
        factory(PlatformAlivod::class)->create([
            'entry_id' => $entry->id,
            'status' => PlatformAlivod::STATUS_READY,
        ]);

        $video = Entry::whereHasPlatformVideos()->first();
        $this->assertEquals($entry->id, $video->id);
    }

    /**
     * @test
     */
    public function test_get_sp_entry_timestamp()
    {
        $sp = factory(PropertySP::class)->create();
        $entry = factory(Entry::class)->create();
        $entry->properties()->attach($sp->id, ['title' => 'title']);

        $entry->refresh();

        $entry->properties()->get()->map(function ($property) use ($sp, $entry) {
            $this->assertEquals($property->pivot->updated_at->timestamp, $entry->getSpEntryTimestamp($sp->id));
        });
    }

    /**
     * @test
     */
    public function test_get_relation_name_from_platform()
    {
        $this->assertEquals('platformAlivod', Entry::getRelationNameFromPlatform(Entry::PLATFORM_ALIVOD));
        $this->assertNull(Entry::getRelationNameFromPlatform('test'));
    }

    /**
     * @test
     */
    public function test_find_by_platform_and_property()
    {
        $cp = factory(PropertyCP::class)->create();
        $entry = factory(Entry::class)->create([
            'property_id' => $cp->id,
        ]);
        $platform_alivod = factory(PlatformAlivod::class)->create([
            'entry_id' => $entry->id,
            'video_id' => '0_4usthaal',
        ]);

        $video = Entry::findByPlatformAndProperty($cp->id, Entry::PLATFORM_ALIVOD, $platform_alivod->video_id);

        $this->assertEquals($entry->id, $video->id);
    }

    /**
     * @test
     */
    public function test_create_with_platform()
    {
        $property = factory(PropertyCP::class)->create();
        $user = factory(User::class)->create();

        $faker = Factory::create();

        $entryAttrs = [
            'name' => $faker->name(),
            'description' => $faker->text(),
            'published_at' => Carbon::now(),
            'status' => Entry::STATUS_READY,
        ];

        $platformAttrs = [
            'status' => PlatformAlivod::STATUS_PROCESSING,
        ];

        Entry::createWithPlatform(
            $property->id,
            $user->id,
            $entryAttrs,
            [],
            Entry::PLATFORM_ALIVOD,
            $platformAttrs
        );

        $this->assertDatabaseHas('entries', $entryAttrs);
        $this->assertDatabaseHas('platform_alivods', $platformAttrs);
    }

    /**
     * @test
     */
    public function test_has_platform()
    {
        $entry = factory(Entry::class)->create([
            'platforms' => 'alivod,test',
        ]);

        $this->assertFalse($entry->hasPlatform('test'));
        $this->assertTrue($entry->hasPlatform('alivod'));
    }

    /**
     * @test
     */
    public function test_has_published_playlist()
    {
        $entry = factory(Entry::class)->create();

        $entry->playlists()->attach(
            factory(Playlist::class)->create([
                'publish_status' => Playlist::PUBLISH_STATUS_PUBLISHED,
            ])->id
        );

        $entry->load('playlists');

        $this->assertTrue($entry->hasPublishedPlaylist());
    }

    /**
     * @test
     */
    public function test_find_sp_entry_by_id_and_playlist()
    {
        $entry = factory(Entry::class)->create();

        $playlistId1 = factory(Playlist::class)->create()->id;
        $playlistId2 = factory(Playlist::class)->create()->id;

        $entry->playlists()->attach([$playlistId1, $playlistId2]);

        $entry->refresh();

        $spEntry = $entry->findSpEntryByIdAndPlaylist($entry->id, [$playlistId1, $playlistId2]);

        $this->assertEquals($entry->id, $spEntry->id);
    }

    /**
     * @test
     */
    public function test_get_highest_priority_of_playlists()
    {
        $playlist1 = factory(Playlist::class)->create([
            'priority' => 2,
        ]);
        $playlist2 = factory(Playlist::class)->create([
            'priority' => 1,
        ]);
        $playlist3 = factory(Playlist::class)->create([
            'priority' => 0,
        ]);
        $entry = factory(Entry::class)->create();
        $entry->playlists()->attach([$playlist1->id, $playlist2->id, $playlist3->id]);

        $this->assertEquals(0, $entry->fresh()->getPriority());
    }

    /**
     * @test
     */
    public function test_add_to_playlist()
    {
        $entry = factory(Entry::class)->create();
        $playlist = factory(Playlist::class)->create([
            'property_id' => $entry->property_id,
        ]);
        $entry->addToPlaylist($playlist);
        $this->assertDatabaseHas('entry_playlist', [
            'entry_id' => $entry->id,
            'playlist_id' => $playlist->id,
        ]);
    }

    /**
     * @test
     */
    public function test_get_entry_unique_genre()
    {
        $entry = factory(Entry::class)->create();
        $playlist1 = factory(Playlist::class)->create(['genre' => 'abc']);
        $playlist2 = factory(Playlist::class)->create(['genre' => 'abc']);
        $playlist3 = factory(Playlist::class)->create(['genre' => 'cde']);

        $entry->playlists()->attach($playlist1->id);
        $entry->playlists()->attach($playlist2->id);
        $entry->playlists()->attach($playlist3->id);

        $entry->load('playlists');

        $this->assertEquals(3, $entry->playlists->count());

        $this->assertEquals(2, count($entry->getGenre()));
        $this->assertTrue(in_array('abc', $entry->getGenre()));
        $this->assertTrue(in_array('cde', $entry->getGenre()));
    }

    /**
     * @test
     */
    public function get_entry_valid_and_unique_platforms()
    {
        $platforms = 'kaltura,kaltura,test,alivod';
        $entry = factory(Entry::class)->create([
            'platforms' => $platforms,
        ]);
        $this->assertNotEquals($platforms, $entry->platforms);
        $this->assertEquals('alivod', $entry->platforms);
    }

    /**
     * @test
     */
    public function test_platforms()
    {
        $entry = factory(Entry::class)->create([
            'platforms' => 'alivod,test',
        ]);

        $this->assertEquals('alivod', $entry->platforms);
    }

    /**
     * @test
     */
    public function test_owner_relationship()
    {
        $entry = factory(Entry::class)->create();

        $this->assertDatabaseHas('users', $entry->owner->first()->toArray());
    }

    /**
     * @test
     */
    public function test_content_provider_relationship()
    {
        $entry = factory(Entry::class)->create();

        $this->assertDatabaseHas('properties', $entry->content_provider->first()->toArray());
    }

    /**
     * @test
     */
    public function test_content_provider_property_relationship()
    {
        $entry = factory(Entry::class)->create();

        $this->assertDatabaseHas('properties', $entry->content_provider_property->first()->toArray());
    }

    /**
     * @test
     */
    public function test_metadata_relationship()
    {
        $entry = factory(Entry::class)->create();
        factory(EntryMeta::class)->create(['entry_id' => $entry->id]);

        $this->assertDatabaseHas('entry_metas', $entry->metadata->toArray());
    }

    /**
     * @test
     */
    public function test_localizations_relationship()
    {
        $entry = factory(Entry::class)->create();
        factory(EntryLocalization::class)->create(['entry_id' => $entry->id]);

        $this->assertDatabaseHas('entry_localizations', $entry->localizations->first()->toArray());
    }

    /**
     * @test
     */
    public function test_analytics_relationship()
    {
        $entry = factory(Entry::class)->create();
        factory(EntryAnalytic::class)->create(['entry_id' => $entry->id]);

        $this->assertDatabaseHas('entry_analytics', $entry->analytics->first()->toArray());
    }

    /**
     * @test
     */
    public function test_subtitles_relationship()
    {
        $entry = factory(Entry::class)->create();
        factory(EntrySubtitle::class)->create(['entry_id' => $entry->id]);

        $this->assertDatabaseHas('entry_subtitles', $entry->subtitles->first()->toArray());
    }

    /**
     * @test
     */
    public function test_playlists_relationship()
    {
        $entry = factory(Entry::class)->create();
        $playlist = factory(Playlist::class)->create();
        $entry->playlists()->attach($playlist->id);

        $entry->refresh();

        $this->assertDatabaseHas('playlists', $entry->playlists->first()->toArray());
    }

    /**
     * @test
     */
    public function test_properties_relationship()
    {
        $sp = factory(PropertySP::class)->create();
        $entry = factory(Entry::class)->create();
        $entry->properties()->attach($sp->id, ['title' => 'title']);

        $entry->refresh();

        $entry->properties->map(function ($property) {
            $this->assertDatabaseHas('properties', $property->toArray());
            $this->assertDatabaseHas('entry_property', $property->pivot->toArray());
        });
    }

    /**
     * @test
     */
    public function test_platformAlivod_relationship()
    {
        $entry = factory(Entry::class)->create();
        factory(PlatformAlivod::class)->create(['entry_id' => $entry->id]);

        $this->assertDatabaseHas('platform_alivods', $entry->platformAlivod->toArray());
    }
}
