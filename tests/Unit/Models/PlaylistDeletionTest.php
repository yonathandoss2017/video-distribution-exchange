<?php

namespace Tests\Unit\Models;

use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\PlaylistProperty;
use App\Models\PlaylistEvidenceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistDeletionTest extends BaseTestCase
{
    use RefreshDatabase;

    protected $playlistSmall;                     //create 10 entries
    protected $playlistLarge;                     //create 100 entries

    public function setUp()
    {
        parent::setUp();
        $this->seedBase();
        $this->loginAsSuperAdmin();
        $this->init_playlist_and_related_datas($this->playlistSmall, 10);
        $this->init_playlist_and_related_datas($this->playlistLarge, 100);
    }

    private function init_playlist_and_related_datas(&$playlist, $entryNum)
    {
        $cp = factory(PropertyCP::class)->create();

        $playlist = factory(Playlist::class)->create([
            'property_id' => $cp->id,
        ]);

        for ($i = 0; $i < $entryNum; ++$i) {
            $entry = factory(Entry::class)->create([
                'user_id' => $this->super_admin->id,
                'property_id' => $cp->id,
            ]);
            $entry->addToPlaylist($playlist);
        }

        $sp = factory(PropertySP::class)->create();

        factory(PlaylistProperty::class)->create([
            'property_id' => $sp->id,
            'playlist_id' => $playlist->id,
        ]);

        factory(PlaylistEvidenceRequest::class)->create([
            'property_id' => $cp->id,
            'playlist_id' => $playlist->id,
        ]);
    }

    /**
     * @test
     */
    public function test_delete()
    {
        $this->assert_delete_small_playlist_and_related_data_success();
        $this->assert_delete_large_playlist_and_related_data_success();
    }

    private function assert_delete_small_playlist_and_related_data_success()
    {
        $this->assertDatabaseHas('playlists', Playlist::find($this->playlistSmall->id)->toArray());

        $this->playlistSmall->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', $entries->toArray());
        });

        $this->assertDatabaseHas('playlist_property', PlaylistProperty::where('playlist_id', $this->playlistSmall->id)->first()->toArray());

        $this->assertDatabaseHas('playlist_evidence_requests', $this->playlistSmall->evidenceRequest->toArray());

        $this->playlistSmall->delete();

        $deletedPlaylist = Playlist::withTrashed()->find($this->playlistSmall->id);

        $this->check_playlist_related_data_deleted($deletedPlaylist, 'small');
    }

    private function assert_delete_large_playlist_and_related_data_success()
    {
        $this->assertDatabaseHas('playlists', Playlist::find($this->playlistLarge->id)->toArray());

        $this->playlistLarge->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', $entries->toArray());
        });

        $this->assertDatabaseHas('playlist_property', PlaylistProperty::where('playlist_id', $this->playlistLarge->id)->first()->toArray());

        $this->assertDatabaseHas('playlist_evidence_requests', $this->playlistLarge->evidenceRequest->toArray());

        $this->playlistLarge->delete();

        $deletedPlaylist = Playlist::withTrashed()->find($this->playlistLarge->id);

        $this->check_playlist_related_data_deleted($deletedPlaylist, 'large');
    }

    private function check_playlist_related_data_deleted($deletedPlaylist, $type)
    {
        $this->assertSoftDeleted('playlists', $deletedPlaylist->toArray());

        $this->assertEquals(0, $deletedPlaylist->entries()->withTrashed()->count());

        $this->assertDatabaseMissing('playlist_property', [
            'playlist_id' => $deletedPlaylist->id,
        ]);

        $this->assertSoftDeleted('playlist_evidence_requests', $deletedPlaylist->evidenceRequest()->withTrashed()->first()->toArray());
    }
}
