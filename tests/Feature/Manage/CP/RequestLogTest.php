<?php

namespace Tests\Feature\Manage\CP;

use Faker\Factory;
use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\PlatformAlivod;
use App\Models\AiReviewVideoResult;
use App\Models\EntryAiReviewResult;
use Illuminate\Support\Facades\Route;
use App\Models\EntryPlaylistRequestReviewViewData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestLogTest extends BaseTestCase
{
    use RefreshDatabase;

    private $entry_draft = null;
    private $entry_processing = null;
    private $entry_pending = null;
    private $entry_ready = null;
    private $entry_rejected = null;
    private $entry_live = null;
    private $entry_stopped = null;
    private $entry_scheduled = null;
    private $entry_error = null;
    private $playlist_pending = null;
    private $playlist_reject = null;
    private $playlist_approve = null;
    private $playlist_draft = null;
    private $ai_review_video_result1 = null;
    private $ai_review_video_result2 = null;

    protected function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function seedPlaylistDatas()
    {
        $this->playlist_pending = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_PENDING,
        ]);
        $this->playlist_reject = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_REJECTED,
        ]);
        $this->playlist_approve = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_READY,
        ]);
        $this->playlist_draft = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_DRAFT,
        ]);
    }

    private function seedEntryDatas()
    {
        $this->entry_draft = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_DRAFT,
        ]);
        $this->entry_proccessing = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_PROCESSING,
        ]);
        $this->entry_pending = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_PENDING,
        ]);
        $this->entry_ready = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_READY,
        ]);
        $this->entry_rejected = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_REJECTED,
        ]);
        $this->entry_scheduled = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_SCHEDULED,
        ]);
        $this->entry_error = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_ERROR,
        ]);
    }

    private function seedEntryAiReviewResult()
    {
        $review_id = factory(EntryAiReviewResult::class)->create([
            'entry_id' => $this->entry_pending->id,
        ]);
        $this->ai_review_video_result1 = factory(AiReviewVideoResult::class)->create([
            'review_id' => $review_id,
            'porn_label' => '',
            'terrorism_label' => 'weapon',
        ]);
        $this->ai_review_video_result2 = factory(AiReviewVideoResult::class)->create([
            'review_id' => $review_id,
            'porn_label' => 'sexy',
            'terrorism_label' => '',
        ]);
    }

    private function loopRoutes($assert = 401)
    {
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $routes) {
            if (0 === strpos($routes->uri, 'manage/{property}/cp/request-logs')) {
                switch ($routes->action['as']) {
                    case 'manage.cp.request-logs.index':
                        $response = $this->get(route('manage.cp.request-logs.index', $this->cp->id));
                        break;
                    case 'manage.cp.request-logs.video.show':
                        $response = $this->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]));
                        break;
                    case 'manage.cp.request-logs.playlist.show':
                        $response = $this->get(route('manage.cp.request-logs.playlist.show', [$this->cp->id, $this->playlist_pending->id]));
                        break;
                    case 'manage.cp.request-logs.comment.edit':
                        $response = $this->get(route('manage.cp.request-logs.comment.edit', [$this->cp->id, $this->playlist_pending->id, 'Playlist']));
                        break;
                    case 'manage.cp.request-logs.approve':
                        $response = $this->post(route('manage.cp.request-logs.approve', [$this->cp->id, $this->entry_pending->id]), [
                            'type' => 'Entry',
                        ]);
                        break;
                    case 'manage.cp.request-logs.reject':
                        $response = $this->post(route('manage.cp.request-logs.reject', [$this->cp->id, $this->playlist_pending->id]), [
                            'type' => 'Playlist',
                        ]);
                        break;
                    case 'manage.cp.request-logs.ai-review':
                        $response = $this->post(route('manage.cp.request-logs.ai-review', [$this->cp->id, $this->entry_pending->id]));
                        break;
                    case 'manage.cp.request-logs.comment.store':
                        $faker = Factory::create();
                        $comment = $faker->text(190);
                        $response = $this->put(route('manage.cp.request-logs.comment.store', [$this->cp->id, $this->entry_pending->id, 'Entry']), [
                                'comments' => $comment,
                        ]);
                        break;
                    case 'manage.cp.request-logs.approve.bulk':
                        $this->entry_pending = factory(Entry::class)->create([
                            'property_id' => $this->cp->id,
                            'user_id' => $this->content_uploader->id,
                            'status' => Entry::STATUS_PENDING,
                        ]);
                        $this->playlist_pending = factory(Playlist::class)->create([
                            'property_id' => $this->cp->id,
                            'user_id' => $this->content_uploader->id,
                            'status' => Playlist::STATUS_PENDING,
                        ]);
                        $response = $this->post(route('manage.cp.request-logs.approve.bulk', $this->cp->id), [
                                'approve_items' => 'Playlist-'.$this->playlist_pending->id.',Entry-'.$this->entry_pending->id,
                        ]);
                        break;
                    case 'manage.cp.request-logs.reject.bulk':
                        $this->entry_pending = factory(Entry::class)->create([
                            'property_id' => $this->cp->id,
                            'user_id' => $this->content_uploader->id,
                            'status' => Entry::STATUS_PENDING,
                        ]);
                        $this->playlist_pending = factory(Playlist::class)->create([
                            'property_id' => $this->cp->id,
                            'user_id' => $this->content_uploader->id,
                            'status' => Playlist::STATUS_PENDING,
                        ]);
                        $response = $this->post(route('manage.cp.request-logs.reject.bulk', $this->cp->id), [
                            'reject_items' => 'Playlist-'.$this->playlist_pending->id.',Entry-'.$this->entry_pending->id,
                        ]);
                        break;
                    case 'manage.cp.request-logs.ai-review.bulk':
                        $response = $this->post(route('manage.cp.request-logs.ai-review.bulk', $this->cp->id), [
                            'ai_review_items' => 'Playlist-'.$this->playlist_pending->id.',Entry-'.$this->entry_pending->id,
                        ]);
                        break;
                    case 'manage.cp.request-logs.ai-review.result':
                        $response = $this->get(route('manage.cp.request-logs.ai-review.result', [$this->cp->id, $this->entry_pending->id]));
                        break;
                    default:
                        $response = $this;
                        break;
                }

                switch ($assert) {
                    case 401:
                        $response->assertRedirect('login');
                        break;
                    case 403:
                        $response->assertStatus(403);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * @test
     */
    public function not_authenticated_user_cannot_access()
    {
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $this->loopRoutes();
    }

    /**
     * @test
     */
    public function sp_user_cannot_access()
    {
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $this->actingAs($this->sp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->loopRoutes(403);
    }

    /**
     * @test
     */
    public function content_uploader_can_access_request_logs()
    {
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $response = $this->actingAs($this->content_uploader)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertSee($this->entry_pending->name)
            ->assertSee($this->playlist_pending->name)
            ->assertSee($this->entry_rejected->name)
            ->assertSee($this->playlist_reject->name)
            ->assertDontSee($this->entry_draft->name)
            ->assertDontSee($this->entry_proccessing->name)
            ->assertDontSee($this->entry_ready->name)
            ->assertDontSee($this->entry_scheduled->name)
            ->assertDontSee($this->entry_error->name)
            ->assertDontSee($this->playlist_approve->name)
            ->assertDontSee($this->playlist_draft->name)
            ->assertStatus(200);

        $pendingCount = EntryPlaylistRequestReviewViewData::where([
                'property_id' => $this->cp->id,
            ])
            ->count();
        if ($pendingCount) {
            $response->assertSee('<span class="label label-license">'.$pendingCount.'</span>');
        }
    }

    /**
     * @test
     */
    public function content_uploader_can_view_comment()
    {
        $this->seedEntryDatas();

        $faker = Factory::create();
        $comment = $faker->text(190);
        $this->entry_pending->comment = $comment;
        $this->entry_pending->save();

        $this->actingAs($this->content_uploader)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertSee($comment);
    }

    /**
     * @test
     */
    public function content_uploader_cannot_edit_comment()
    {
        $this->actingAs($this->content_uploader)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertDontSee(__('manage/cp/exchange/request_logs.comments'));

        $this->seedEntryDatas();
        $this->get(route('manage.cp.request-logs.comment.edit', [$this->cp->id, $this->entry_pending->id, 'Entry']))
            ->assertStatus(403);

        $faker = Factory::create();
        $comment = $faker->text(190);
        $this->put(route('manage.cp.request-logs.comment.store', [$this->cp->id, $this->entry_pending->id, 'Entry']), [
                'comments' => $comment,
            ])->assertStatus(403);
    }

    /**
     * @test
     */
    public function content_uploader_can_see_ai_review_when_ai_on()
    {
        $this->organization->feature()->updateOrCreate([
            'organization_id' => $this->organization->id,
        ], [
            'ai_content_review' => 1,
        ]);

        $this->seedEntryDatas();
        $this->seedEntryAiReviewResult();

        $this->actingAs($this->content_uploader)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertSee(__('manage/cp/contents/videos.ai_censorship'));

        $this->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertSee(__('manage/cp/contents/videos.ai_censorship'))
            ->assertSee(__('manage/cp/contents/request_logs.ai_label_'.$this->ai_review_video_result1->terrorism_label))
            ->assertSee(__('manage/cp/contents/request_logs.ai_label_'.$this->ai_review_video_result2->porn_label));
    }

    /**
     * @test
     */
    public function content_uploader_cannot_see_ai_review_when_ai_off()
    {
        $this->organization->feature()->updateOrCreate([
            'organization_id' => $this->organization->id,
        ], [
            'ai_content_review' => 0,
        ]);

        $this->seedEntryDatas();
        $this->seedEntryAiReviewResult();

        $this->actingAs($this->content_uploader)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertDontSee(__('manage/cp/contents/videos.ai_censorship'));

        $this->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertDontSee(__('manage/cp/contents/videos.ai_censorship'));
    }

    /**
     * @test
     */
    public function censor_can_access_and_operate_request_logs()
    {
        $this->seedEntryDatas();
        $this->seedPlaylistDatas();

        $response = $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertSee($this->entry_pending->name)
            ->assertSee($this->playlist_pending->name)
            ->assertSee('<li><a href="javascript:void(0)" onclick="bulk_action(0);">'.__('manage/cp/contents/request_logs.approve').'</a></li>')
            ->assertSee('<li><a href="javascript:void(0)" onclick="bulk_action(1);">'.__('manage/cp/contents/request_logs.reject').'</a></li>')
            ->assertSee('<a href="javascript:void(0);" class="approve-btn" data-id="'.$this->entry_pending->id.'">'.__('manage/cp/exchange/request_logs.approve').'</a>')
            ->assertSee('<a href="javascript:void(0);" class="reject-btn" data-id="'.$this->entry_pending->id.'">'.__('manage/cp/exchange/request_logs.reject').'</a>')
            ->assertSee('<a href="'.route('manage.cp.request-logs.comment.edit', [$this->cp->id, $this->entry_pending->id, 'Entry']).'">'.__('manage/cp/exchange/request_logs.comments').'</a>')
            ->assertSee(__('manage/cp/exchange/request_logs.view'))
            ->assertDontSee($this->entry_draft->name)
            ->assertDontSee($this->entry_proccessing->name)
            ->assertDontSee($this->entry_ready->name)
            ->assertDontSee($this->entry_rejected->name)
            ->assertDontSee($this->entry_scheduled->name)
            ->assertDontSee($this->entry_error->name)
            ->assertDontSee($this->playlist_reject->name)
            ->assertDontSee($this->playlist_approve->name)
            ->assertDontSee($this->playlist_draft->name)
            ->assertStatus(200);

        $pendingCount = EntryPlaylistRequestReviewViewData::where([
                'property_id' => $this->cp->id,
            ])
            ->whereNotIn('status', [Entry::STATUS_REJECTED, Playlist::STATUS_REJECTED])
            ->count();
        if ($pendingCount) {
            $response->assertSee('<span class="label label-license">'.$pendingCount.'</span>');
        }

        $this->post(route('manage.cp.request-logs.approve', [$this->cp->id, $this->entry_pending->id]), [
                'type' => 'Entry',
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));
        $this->assertDatabaseHas('entries', [
            'id' => $this->entry_pending->id,
            'name' => $this->entry_pending->name,
            'status' => Entry::STATUS_READY,
        ]);

        $this->entry_pending = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_PENDING,
        ]);
        $this->post(route('manage.cp.request-logs.reject', [$this->cp->id, $this->entry_pending->id]), [
                'type' => 'Entry',
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));
        $this->assertDatabaseHas('entries', [
            'id' => $this->entry_pending->id,
            'name' => $this->entry_pending->name,
            'status' => Entry::STATUS_REJECTED,
        ]);

        $this->post(route('manage.cp.request-logs.reject', [$this->cp->id, $this->playlist_pending->id]), [
                'type' => 'Playlist',
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));
        $this->assertDatabaseHas('playlists', [
            'id' => $this->playlist_pending->id,
            'name' => $this->playlist_pending->name,
            'status' => Playlist::STATUS_REJECTED,
        ]);

        $this->playlist_pending = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_PENDING,
        ]);
        $this->post(route('manage.cp.request-logs.approve', [$this->cp->id, $this->playlist_pending->id]), [
                'type' => 'Playlist',
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));
        $this->assertDatabaseHas('playlists', [
            'id' => $this->playlist_pending->id,
            'name' => $this->playlist_pending->name,
            'status' => Playlist::STATUS_READY,
        ]);

        $this->entry_pending = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_PENDING,
        ]);
        $this->playlist_pending = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_PENDING,
        ]);
        $this->post(route('manage.cp.request-logs.approve.bulk', $this->cp->id), [
                'approve_items' => 'Playlist-'.$this->playlist_pending->id.',Entry-'.$this->entry_pending->id,
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));

        $this->entry_pending = factory(Entry::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Entry::STATUS_PENDING,
        ]);
        $this->playlist_pending = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_PENDING,
        ]);
        $this->post(route('manage.cp.request-logs.reject.bulk', $this->cp->id), [
                'reject_items' => 'Playlist-'.$this->playlist_pending->id.',Entry-'.$this->entry_pending->id,
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));
    }

    /**
     * @test
     */
    public function censor_can_edit_comment()
    {
        $this->seedEntryDatas();
        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.comment.edit', [$this->cp->id, $this->entry_pending->id, 'Entry']))
            ->assertStatus(200);

        $faker = Factory::create();
        $comment = $faker->text(190);
        $this->put(route('manage.cp.request-logs.comment.store', [$this->cp->id, $this->entry_pending->id, 'Entry']), [
                'comments' => $comment,
            ])
            ->assertSessionHas('success')
            ->assertRedirect(route('manage.cp.request-logs.index', $this->cp->id));

        $this->assertDatabaseHas('entries', [
            'name' => $this->entry_pending->name,
            'comment' => $comment,
        ]);
    }

    /**
     * @test
     */
    public function censor_can_view_comment()
    {
        $this->seedEntryDatas();

        $faker = Factory::create();
        $comment = $faker->text(190);
        $this->entry_pending->comment = $comment;
        $this->entry_pending->save();

        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertSee($comment);

        $this->seedPlaylistDatas();

        $comment = $faker->text(190);
        $this->playlist_pending->comment = $comment;
        $this->playlist_pending->save();

        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.playlist.show', [$this->cp->id, $this->playlist_pending->id]))
            ->assertStatus(200)
            ->assertSee($comment);
    }

    /**
     * @test
     */
    public function censor_can_see_and_submit_ai_review_when_ai_on()
    {
        $this->organization->feature()->create([
            'ai_content_review' => 1,
        ]);

        $this->seedEntryDatas();

        factory(PlatformAlivod::class)->create([
            'entry_id' => $this->entry_pending->id,
        ]);

        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertStatus(200)
            ->assertSee('<li><a href="javascript:void(0)" onclick="bulk_action(2)">'.__('manage/cp/contents/videos.ai_censorship').'</a></li>')
            ->assertSee('<a href="javascript:void(0);" class="ai-review-btn" data-id="'.$this->entry_pending->id.'">'.__('manage/cp/contents/videos.ai_censorship').'</a>');

        $this->seedEntryAiReviewResult();

        $this->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertSee('<h5>'.__('manage/cp/contents/videos.ai_censorship').'</h5>')
            ->assertSee('<span>'.__('manage/cp/contents/request_logs.ai_label_sexy').'</span>')
            ->assertSee('<span>'.__('manage/cp/contents/request_logs.ai_label_weapon').'</span>');
    }

    /**
     * @test
     */
    public function censor_cannot_see_ai_review_when_ai_off()
    {
        $this->organization->feature()->create([
            'ai_content_review' => 0,
        ]);

        $this->seedEntryDatas();
        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.request-logs.index', $this->cp->id))
            ->assertStatus(200)
            ->assertDontSee('<li><a href="javascript:void(0)" onclick="bulk_action(2)">'.__('manage/cp/contents/videos.ai_censorship').'</a></li>')
            ->assertDontSee('<a href="javascript:void(0);" class="ai-review-btn" data-id="'.$this->entry_pending->id.'">'.__('manage/cp/contents/videos.ai_censorship').'</a>');

        $this->seedEntryAiReviewResult();

        $this->get(route('manage.cp.request-logs.video.show', [$this->cp->id, $this->entry_pending->id]))
            ->assertStatus(200)
            ->assertDontSee('<h5>'.__('manage/cp/contents/videos.ai_censorship').'</h5>')
            ->assertDontSee('<span>'.__('manage/cp/contents/request_logs.ai_label_sexy').'</span>')
            ->assertDontSee('<span>'.__('manage/cp/contents/request_logs.ai_label_weapon').'</span>');
    }
}
