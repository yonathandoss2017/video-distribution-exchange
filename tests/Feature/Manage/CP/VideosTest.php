<?php

namespace Tests\Feature\Manage\CP;

use Faker\Factory;
use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PlatformAlivod;
use Illuminate\Http\UploadedFile;
use App\Jobs\Vod\Ali\VideoIngestJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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

    private function seedEntryDatas()
    {
        $this->entry = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
        ]);
        factory(PlatformAlivod::class)->create([
            'entry_id' => $this->entry->id,
            'video_id' => '0_4usthaal',
        ]);
    }

    private function seedPlaylistDatas()
    {
        $this->playlist = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'status' => Playlist::STATUS_READY,
        ]);
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access_video()
    {
        $this->get(route('manage.cp.videos', $this->cp->id))
            ->assertRedirect('login');
    }

    /**
     * @test
     */
    public function access_videos_list_page()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();
        $this->get(route('manage.cp.videos', $this->cp->id))
            ->assertStatus(200)
            ->assertSee(htmlspecialchars($this->entry->name, ENT_QUOTES))
            ->assertSee(__('common.edit'))
            ->assertSee(route('manage.cp.video.delete', [$this->cp->id, $this->entry->id]));
    }

    /**
     * @test
     */
    public function sp_user_cannot_access()
    {
        $this->loginAsManager(Property::TYPE_SP);
        $this->seedEntryDatas();
        $this->get(route('manage.cp.videos', $this->cp->id))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function access_video_add_page()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->get(route('manage.cp.add.video', $this->cp->id))
            ->assertStatus(200)
            ->assertSee(__('manage/cp/contents/videos.new_video'))
            ->assertSee(__('manage/cp/contents/videos.upload_method'))
            ->assertSee(__('manage/cp/contents/videos.video_information'))
            ->assertSee(__('common.save'));
    }

    /**
     * @test
     */
    public function create_new_video_success()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedPlaylistDatas();

        Storage::fake('storage');

        $response = $this->get(route('manage.cp.request-upload', $this->cp->id).'?filename='.str_random(10).'.mp4');

        $response->assertJsonStructure([
            'accessid',
            'host',
            'policy',
            'signature',
            'expire',
            'key',
            'filename',
        ]);

        $faker = Factory::create();
        $file_name = $faker->uuid.'.mp4';
        $video_path = 'tmp/'.$file_name;
        Storage::disk('storage')->put($video_path, UploadedFile::fake()->image('video.mp4'));

        Queue::fake();

        $title = $faker->title;

        $response = $this->post(route('manage.cp.store.video', $this->cp->id), [
            'title' => $title,
            'description' => 'description',
            'tags' => 'tags1',
            'is_scheduled' => 0,
            'img_method' => 'img_direct',
            'imagefile' => UploadedFile::fake()->image('imagefile.jpg'),
            'playlist' => [$this->playlist->id],
            'video_method' => Entry::VIDEO_DIRECT_UPLOAD,
            'video_name' => $file_name,
            'video_path' => $video_path,
        ]);

        Queue::assertPushed(VideoIngestJob::class, function ($job) use ($title) {
            return $job->entry->name === $title;
        });

        $response->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.videos', $this->cp->id));

        $this->followRedirects($response)
            ->assertSee(htmlspecialchars($title, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function status_is_pending_or_processing_after_create()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedPlaylistDatas();

        Storage::fake('storage');

        $response = $this->get(route('manage.cp.request-upload', $this->cp->id).'?filename='.str_random(10).'.mp4');

        $response->assertJsonStructure([
            'accessid',
            'host',
            'policy',
            'signature',
            'expire',
            'key',
            'filename',
        ]);

        $faker = Factory::create();
        $file_name = $faker->uuid.'.mp4';
        $video_path = 'tmp/'.$file_name;
        Storage::disk('storage')->put($video_path, UploadedFile::fake()->image('video.mp4'));

        Queue::fake();

        $title = $faker->title;

        $response = $this->post(route('manage.cp.store.video', $this->cp->id), [
            'title' => $title,
            'description' => 'description',
            'tags' => 'tags1',
            'is_scheduled' => 0,
            'img_method' => 'img_direct',
            'imagefile' => UploadedFile::fake()->image('imagefile.jpg'),
            'playlist' => [$this->playlist->id],
            'video_method' => Entry::VIDEO_DIRECT_UPLOAD,
            'video_name' => $file_name,
            'video_path' => $video_path,
        ]);

        Queue::assertPushed(VideoIngestJob::class, function ($job) use ($title) {
            return $job->entry->name === $title;
        });

        $response->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.videos', $this->cp->id));

        $this->followRedirects($response)
            ->assertSee(htmlspecialchars($title, ENT_QUOTES))
            ->assertSee('<span class="label label-orange">'.__('manage/cp/contents/videos.processing').'</span>');
    }

    /**
     * @test
     */
    public function create_new_video_missing_required_values_failed()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedPlaylistDatas();

        Storage::fake('storage');

        $response = $this->get(route('manage.cp.request-upload', $this->cp->id).'?filename='.str_random(10).'.mp4');

        $response->assertJsonStructure([
            'accessid',
            'host',
            'policy',
            'signature',
            'expire',
            'key',
            'filename',
        ]);

        $faker = Factory::create();
        $file_name = $faker->uuid.'.mp4';
        $video_path = 'tmp/'.$file_name;
        Storage::disk('storage')->put($video_path, UploadedFile::fake()->image('video.mp4'));

        Queue::fake();

        $title = $faker->title;

        $response = $this->post(route('manage.cp.store.video', $this->cp->id), [
            'description' => 'description',
            'tags' => 'tags1',
            'is_scheduled' => 0,
            'img_method' => 'img_direct',
            'imagefile' => UploadedFile::fake()->image('imagefile.jpg'),
            'playlist' => [$this->playlist->id],
            'video_method' => Entry::VIDEO_DIRECT_UPLOAD,
            'video_name' => $file_name,
            'video_path' => $video_path,
        ]);

        Queue::assertNotPushed(VideoIngestJob::class, function ($job) use ($title) {
            return $job->entry->name === $title;
        });

        $response->assertSee('title');
    }

    /**
     * @test
     */
    public function access_video_edit_page()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();

        $this->get(route('manage.cp.video.edit', [$this->cp->id, $this->entry->id]))
            ->assertStatus(200)
            ->assertSee(__('manage/cp/contents/videos.edit_video'))
            ->assertSee(htmlspecialchars($this->entry->name, ENT_QUOTES))
            ->assertSee(__('common.save'));
    }

    /**
     * @test
     */
    public function edit_video_success()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $response = $this->post(route('manage.cp.video.update', [$this->cp->id, $this->entry->id]), [
            'title' => 'video title test',
            'playlist' => [$this->playlist->id],
        ]);

        $response->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.video.edit', [$this->cp->id, $this->entry->id]));

        $this->followRedirects($response)
            ->assertSee('video title test');
    }

    /**
     * @test
     */
    public function edit_video_missing_required_values_failed()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $response = $this->post(route('manage.cp.video.update', [$this->cp->id, $this->entry->id]), [
            'playlist' => [$this->playlist->id],
        ]);

        $response->assertSee('title');
    }

    /**
     * @test
     */
    public function cannot_choose_playlist_which_is_not_approved()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();
        $this->playlist = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'status' => Playlist::STATUS_PENDING,
        ]);

        $response = $this->post(route('manage.cp.video.update', [$this->cp->id, $this->entry->id]), [
            'title' => 'video title test',
            'playlist' => [$this->playlist->id],
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * @test
     */
    public function delete_video_success()
    {
        $this->loginAsManager(Property::TYPE_CP);
        $this->seedEntryDatas();

        $this->delete(route('manage.cp.video.delete', [$this->cp->id, $this->entry->id]))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('entries', $this->entry->fresh()->toArray());
    }

    /**
     * @test
     */
    public function batch_apply_for_review_success()
    {
        $this->loginAsManager(Property::TYPE_CP);

        $entry1 = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'status' => Entry::STATUS_DRAFT,
        ]);
        $entry2 = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'status' => Entry::STATUS_DRAFT,
        ]);

        $response = $this->post(route('manage.cp.video.review.bulk', $this->cp->id), [
            'video_ids' => $entry1->id.','.$entry2->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('entries', [
            'id' => $entry1->id,
            'status' => Entry::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('entries', [
            'id' => $entry2->id,
            'status' => Entry::STATUS_PENDING,
        ]);
    }

    /**
     * @test
     */
    public function batch_delete_videos_success()
    {
        $this->loginAsManager(Property::TYPE_CP);

        $entry1 = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'status' => Entry::STATUS_DRAFT,
        ]);
        $entry2 = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'status' => Entry::STATUS_DRAFT,
        ]);

        $response = $this->delete(route('manage.cp.video.delete.bulk', $this->cp->id), [
            'video_ids' => $entry1->id.','.$entry2->id,
        ])->assertSessionHas('success');
    }
}
