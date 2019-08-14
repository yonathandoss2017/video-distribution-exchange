<?php

namespace Tests\Unit\Models;

use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\EntryMeta;
use App\Models\PropertySP;
use App\Models\EntryAnalytic;
use App\Models\PlatformAlivod;
use App\Models\EntryLocalization;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntryDeletionTest extends BaseTestCase
{
    use RefreshDatabase;

    protected $entry;
    protected $entryId;
    protected $entry_playlist1;
    protected $entry_playlist2;
    protected $entry_playlist3;
    protected $entry_property1;
    protected $entry_property2;
    protected $entry_property3;

    public function setUp()
    {
        parent::setUp();
        $this->addEntry();
        $this->addEntryRelatedData();
    }

    public function addEntry()
    {
        $this->seedBase();
        $this->loginAsSuperAdmin();
        $this->entry = factory(Entry::class)->create([
            'user_id' => $this->super_admin->id,
            'property_id' => $this->cp->id,
        ]);
        $this->entryId = $this->entry->id;
    }

    public function addEntryRelatedData()
    {
        factory(EntryMeta::class)->create([
            'entry_id' => $this->entryId,
        ]);
        factory(EntryLocalization::class, 2)->create([
            'entry_id' => $this->entryId,
        ]);
        factory(EntryAnalytic::class, 2)->create([
            'entry_id' => $this->entryId,
            'property_id' => function () {
                return factory(PropertySP::class)->create()->id;
            },
        ]);
        factory(PlatformAlivod::class)->create([
            'entry_id' => $this->entryId,
        ]);
        if (!Playlist::find(1)) {
            $feed1 = factory(Playlist::class)->create();
            $feed1->id = 1;
            $feed1->save();
        }
        if (!Playlist::find(2)) {
            $feed2 = factory(Playlist::class)->create();
            $feed2->id = 2;
            $feed2->save();
        }
        if (!Playlist::find(3)) {
            $feed3 = factory(Playlist::class)->create();
            $feed3->id = 3;
            $feed3->save();
        }
        $this->entry_playlist1 = ['entry_id' => $this->entryId, 'playlist_id' => 1];
        $this->entry_playlist2 = ['entry_id' => $this->entryId, 'playlist_id' => 2];
        $this->entry_playlist3 = ['entry_id' => $this->entryId, 'playlist_id' => 3];
        DB::table('entry_playlist')->insert([
            $this->entry_playlist1,
            $this->entry_playlist2,
            $this->entry_playlist3,
        ]);
        $this->entry_property1 = ['entry_id' => $this->entryId, 'property_id' => 3000002, 'title' => 'abc', 'description' => 'ads'];
        $this->entry_property2 = ['entry_id' => $this->entryId, 'property_id' => 3000004, 'title' => 'abc', 'description' => 'ads'];
        $this->entry_property3 = ['entry_id' => $this->entryId, 'property_id' => 3000197, 'title' => 'abc', 'description' => 'ads'];
        DB::table('entry_property')->insert([
            $this->entry_property1,
            $this->entry_property2,
            $this->entry_property3,
        ]);
    }

    /**
     * @test
     * Entry deletion test.
     */
    public function deleteEntryWithRelatedDataAsSuperadminSuccess()
    {
        $this->assertDatabaseHas('entries', Entry::find($this->entry->id)->toArray());
        $this->assertDatabaseHas('entry_metas', $this->entry->metadata->toArray());
        $this->entry->localizations->map(function ($localization) {
            $this->assertDatabaseHas('entry_localizations', $localization->toArray());
        });
        $this->entry->analytics->map(function ($analytic) {
            $this->assertDatabaseHas('entry_analytics', $analytic->toArray());
        });
        $this->assertDatabaseHas('platform_alivods', $this->entry->platformAlivod->toArray());
        $this->entry->playlists->map(function ($playlist) {
            $this->assertDatabaseHas('entry_playlist', $playlist->pivot->toArray());
        });
        $this->entry->properties->map(function ($property) {
            $this->assertDatabaseHas('entry_property', $property->pivot->toArray());
        });

        //delete entry
        $this->entry->delete();

        $deletedEntry = Entry::withTrashed()->find($this->entryId);
        $this->assertSoftDeleted('entries', $deletedEntry->toArray());
        $this->assertSoftDeleted('entry_metas', $deletedEntry->metadata()->withTrashed()->first()->toArray());
        $deletedEntry->localizations()->withTrashed()->get()->map(function ($localization) {
            $this->assertSoftDeleted('entry_localizations', $localization->toArray());
        });
        $deletedEntry->analytics()->withTrashed()->get()->map(function ($analytic) {
            $this->assertSoftDeleted('entry_analytics', $analytic->toArray());
        });
        $this->assertSoftDeleted('platform_alivods', $deletedEntry->platformAlivod()->withTrashed()->first()->toArray());
        $this->assertDatabaseMissing('entry_playlist', $this->entry_playlist1);
        $this->assertDatabaseMissing('entry_playlist', $this->entry_playlist2);
        $this->assertDatabaseMissing('entry_playlist', $this->entry_playlist3);
        $this->assertDatabaseMissing('entry_property', $this->entry_property1);
        $this->assertDatabaseMissing('entry_property', $this->entry_property2);
        $this->assertDatabaseMissing('entry_property', $this->entry_property3);
    }
}
