<?php

namespace Tests\Feature\Manage\SP;

use Carbon\Carbon;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PlaylistProperty;
use App\Models\LicenseNotification;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;
use App\Services\Serve\PlaylistDetailsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function init_sp_playlists()
    {
        $tod = factory(TermsOfDistribution::class)->create([
            'sp_property_id' => $this->sp->id,
            'cp_property_id' => $this->cp->id,
            'status' => TermsOfDistribution::STATUS_ACTIVE,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->firstOfMonth(),
            'ended_at' => Carbon::now()->lastOfMonth(),
        ]);
        factory(LicenseNotification::class)->create(['property_id' => $this->cp->id, 'user_id' => $this->cp_manager->id]);
        $playlist = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'status' => Playlist::STATUS_READY,
        ]);
        PlaylistProperty::createOrUpdateRecord($this->cp->id, $this->sp->id, $playlist->id);
        $tod->playlists()->attach($playlist->id);

        return [
            'tod' => $tod,
            'playlist' => $playlist,
        ];
    }

    /** @test */
    public function sp_delete_playlist()
    {
        $this->loginAsManager(Property::TYPE_SP);

        $data = $this->init_sp_playlists();
        $tod = $data['tod'];
        $playlist = $data['playlist'];

        $playlistProperties = PlaylistProperty::findRecord($this->cp->id, $this->sp->id, $playlist->id)->get();

        $response = $this->delete(route('manage.sp.playlists.destroy', ['property_id' => $this->sp->id, 'id' => $playlist->id]));

        foreach ($playlistProperties as $pp) {
            $this->assertDatabaseMissing('playlist_property', $pp->toArray());
        }

        $tod->playlistsWithTrashed()->get()->each(function ($tod_playlist) {
            $this->assertSoftDeleted('playlist_terms_of_distribution', $tod_playlist->pivot->toArray());
        });

        $response->assertSessionHas('success', __('manage/sp/content/playlist.playlist_deleted_success'));
    }

    /** @test */
    public function sp_visit_playlist()
    {
        $this->loginAsManager(Property::TYPE_SP);

        $data = $this->init_sp_playlists();
        $tod = $data['tod'];
        $playlist = $data['playlist'];

        $this->get(route('manage.sp.playlists.index', $this->sp->id))
            ->assertStatus(200)
            ->assertSee(htmlspecialchars(PlaylistDetailsService::getName($playlist, $this->sp->id), ENT_QUOTES))
            ->assertSee(route('manage.sp.playlist.edit', [$this->sp->id, $playlist->id]));
    }

    /** @test */
    public function sp_edit_playlist()
    {
        $this->loginAsManager(Property::TYPE_SP);

        $data = $this->init_sp_playlists();
        $tod = $data['tod'];
        $playlist = $data['playlist'];

        $this->get(route('manage.sp.playlist.edit', [$this->sp->id, $playlist->id]))
            ->assertStatus(200)
            ->assertSee(htmlspecialchars(PlaylistDetailsService::getName($playlist, $this->sp->id), ENT_QUOTES))
            ->assertSee(htmlspecialchars($tod->name, ENT_QUOTES));
    }
}
