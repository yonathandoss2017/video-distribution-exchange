<?php

namespace Tests\Feature\Manage\SP;

use Carbon\Carbon;
use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PlatformAlivod;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideosTest extends BaseTestCase
{
    use RefreshDatabase;

    private $entry = null;
    private $playlist = null;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function seedSpVideoDatas()
    {
        $this->entry = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'status' => Entry::STATUS_READY,
        ]);

        factory(PlatformAlivod::class)->create([
            'entry_id' => $this->entry->id,
            'video_id' => '0_4usthaal',
        ]);

        $this->playlist = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'status' => Playlist::STATUS_READY,
        ]);

        $this->entry->playlists()->attach($this->playlist->id);

        $tod = factory(TermsOfDistribution::class)->create([
            'status' => TermsOfDistribution::STATUS_ACTIVE,
            'cp_organization_id' => $this->cp->organization_id,
            'cp_property_id' => $this->cp->id,
            'sp_property_id' => $this->sp->id,
            'creator' => $this->cp_manager->id,
            'updater' => $this->cp_manager->id,
        ]);
        factory(DistributionRegionRight::class)->create([
            'tod_id' => $tod->id,
            'started_at' => Carbon::now()->firstOfMonth(),
            'ended_at' => Carbon::now()->lastOfMonth(),
        ]);
        $tod->playlists()->sync($this->playlist->id);
    }

    private function initVideoDatasAndLogin()
    {
        $this->loginAsManager(Property::TYPE_SP);
        $this->seedSpVideoDatas();
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access_video()
    {
        $this->get(route('manage.sp.videos', $this->sp->id))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function access_videos_list_page()
    {
        $this->initVideoDatasAndLogin();

        $this->get(route('manage.sp.videos', $this->sp->id))
            ->assertStatus(200)
            ->assertSee(htmlspecialchars($this->entry->name, ENT_QUOTES))
            ->assertSee(__('manage/sp/common.edit'));
    }

    /**
     * @test
     */
    public function access_video_edit_page()
    {
        $this->initVideoDatasAndLogin();

        $this->get(route('manage.sp.video.edit', [$this->sp->id, $this->entry->id]))
            ->assertStatus(200)
            ->assertSee(__('manage/sp/content/video.edit_video'))
            ->assertSee(htmlspecialchars($this->entry->name, ENT_QUOTES))
            ->assertSee(__('manage/sp/common.save'));
    }

    /**
     * @test
     */
    public function edit_video_success()
    {
        $this->initVideoDatasAndLogin();

        $response = $this->post(route('manage.sp.video.update', [$this->sp->id, $this->entry->id]), [
            'title' => 'video title test',
        ]);

        $response->assertSessionHas('success')
            ->assertRedirect(route('manage.sp.video.edit', [$this->sp->id, $this->entry->id]));

        $this->followRedirects($response)
            ->assertSee('video title test');
    }
}
