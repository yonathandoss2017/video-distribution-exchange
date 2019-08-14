<?php

namespace Tests\Feature\Manage\CP;

use Carbon\Carbon;
use Faker\Factory;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\TermsOfMarketplace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistTest extends BaseTestCase
{
    use RefreshDatabase;

    private $playlist_pending = null;
    private $playlist_reject = null;
    private $playlist_approve = null;
    private $playlist_draft = null;

    private $acting_as_user = null;

    public function setUp()
    {
        parent::setUp();
        $this->seedBase();
    }

    private function seed_playlist_datas()
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

    private function reset_empty_datas_for_playlist()
    {
        Playlist::withTrashed()->forceDelete();
    }

    private function access_routes($assert = 401)
    {
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $routes) {
            if (0 === strpos($routes->uri, 'manage/{property}/cp/playlists')) {
                switch ($routes->action['as']) {
                    case 'manage.cp.playlists.index':
                        $response = $this->get(route('manage.cp.playlists.index', $this->cp->id));
                        break;
                    case 'manage.cp.playlists.create':
                        $response = $this->get(route('manage.cp.playlists.create', $this->cp->id));
                        break;
                    case 'manage.cp.playlists.store':
                        $response = $this->post(route('manage.cp.playlists.store', $this->cp->id), [
                            'name' => 'Playlist',
                            'genre' => 'food',
                            'region' => 'CN',
                            'language' => 'zh-Hans',
                        ]);
                        break;
                    case 'manage.cp.playlists.edit':
                        $response = $this->get(route('manage.cp.playlists.edit', [$this->cp->id, $this->playlist_pending->id]));
                        break;
                    case 'manage.cp.playlists.update':
                        $response = $this->put(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_pending->id]), [
                            'name' => 'Playlist',
                            'genre' => 'food',
                            'region' => 'CN',
                            'language' => 'zh-Hans',
                        ]);
                        break;
                    case 'manage.cp.playlists.publish':
                        $response = $this->get(route('manage.cp.playlists.publish', [$this->cp->id, $this->playlist_approve->id]));
                        break;
                    case 'manage.cp.playlists.update-publish':
                        $response = $this->put(route('manage.cp.playlists.update-publish', [$this->cp->id, $this->playlist_approve->id]), [
                            'radio_publish' => 'off',
                        ]);
                        break;
                    case 'manage.cp.playlists.destroy':
                        $response = $this->delete(route('manage.cp.playlists.destroy', [$this->cp->id, $this->playlist_pending->id]));
                        break;
                    case 'manage.cp.playlists.review.request':
                        $response = $this->post(route('manage.cp.playlists.review.request', $this->cp->id), [
                            'playlist_ids' => $this->playlist_draft->id,
                        ]);
                        break;
                    case 'manage.cp.playlists.get_playlist':
                        $response = $this->get(route('manage.cp.playlists.get_playlist', $this->cp->id));
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
        $this->seed_playlist_datas();
        $this->access_routes();
    }

    /**
     * @test
     */
    public function sp_user_cannot_access()
    {
        $this->seed_playlist_datas();
        $this->actingAs($this->sp_manager)
            ->withSession(['organization' => $this->organization->id]);

        $this->access_routes(403);
    }

    /**
     * @test
     */
    public function content_uploader_operate_playlist()
    {
        $this->acting_as_user = $this->actingAs($this->content_uploader)->withSession(['organization' => $this->organization->id]);

        $this->access_playlist();
        $this->create_playlist();
        $this->store_playlist_without_required_values_failed();
        $this->store_playlist_with_all_required_values_success();
        $this->edit_playlist();
        $this->update_playlist_without_required_values_failed();
        $this->update_playlist_with_all_required_values_success();
        $this->publish_playlist();
        $this->can_request_review_when_content_review_feature_on();
        $this->cannot_request_review_when_content_review_feature_off();
        $this->delete_playlist();
    }

    /**
     * @test
     */
    public function censor_operate_playlist()
    {
        $this->acting_as_user = $this->actingAs($this->censor)->withSession(['organization' => $this->organization->id]);

        $this->access_playlist();
        $this->edit_playlist();
        $this->update_playlist_without_required_values_failed();
        $this->update_playlist_with_all_required_values_success();
        $this->publish_playlist();
        $this->can_request_review_when_content_review_feature_on();
        $this->cannot_request_review_when_content_review_feature_off();
        $this->delete_playlist();
    }

    /**
     * @test
     */
    public function censor_cannot_create_and_store_playlist()
    {
        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->get(route('manage.cp.playlists.create', $this->cp->id))
            ->assertDontSee(route('manage.cp.playlists.store', $this->cp->id))
            ->assertStatus(403);

        $this->actingAs($this->censor)
            ->withSession(['organization' => $this->organization->id])
            ->post(route('manage.cp.playlists.store', $this->cp->id), [])
            ->assertSessionMissing([
                'name', 'genre', 'region', 'language',
            ])
            ->assertStatus(403);
    }

    private function access_playlist()
    {
        $this->seed_playlist_datas();

        $response = $this->acting_as_user
            ->get(route('manage.cp.playlists.index', $this->cp->id))
            ->assertSee($this->playlist_pending->name)
            ->assertSee($this->playlist_reject->name)
            ->assertSee($this->playlist_approve->name)
            ->assertSee($this->playlist_draft->name)
            ->assertSee('<a href="'.route('manage.cp.playlists.publish', [$this->cp->id, $this->playlist_approve->id]).'">'.__('common.publish').'</a>')
            ->assertDontSee('<a href="'.route('manage.cp.playlists.publish', [$this->cp->id, $this->playlist_pending->id]).'">'.__('common.publish').'</a>')
            ->assertDontSee('<a href="'.route('manage.cp.playlists.publish', [$this->cp->id, $this->playlist_draft->id]).'">'.__('common.publish').'</a>')
            ->assertDontSee('<a href="'.route('manage.cp.playlists.publish', [$this->cp->id, $this->playlist_reject->id]).'">'.__('common.publish').'</a>')
            ->assertStatus(200);

        $user = Auth::user();
        if ($user->isCensor($this->cp)) {
            $response->assertDontSee(route('manage.cp.playlists.create', $this->cp->id));
            $response->assertDontSee(__('manage/cp/contents/playlists.new_playlist'));
        } else {
            $response->assertSee(route('manage.cp.playlists.create', $this->cp->id));
            $response->assertSee(__('manage/cp/contents/playlists.new_playlist'));
        }

        $this->reset_empty_datas_for_playlist();
    }

    private function create_playlist()
    {
        //content review feature on
        config(['features.content_review' => true]);
        $this->acting_as_user
            ->get(route('manage.cp.playlists.create', $this->cp->id))
            ->assertSee(route('manage.cp.playlists.store', $this->cp->id))
            ->assertSee(__('manage/cp/contents/playlists.new_playlist'))
            ->assertSee('<button type="submit" class="btn btn-primary">'.__('common.save').'</button>')
            ->assertSee('<button type="button" class="btn btn-primary" onclick="submitForm()">'.__('common.save_and_submit').'</button>')
            ->assertStatus(200);

        //content review feature off
        config(['features.content_review' => false]);
        $this->acting_as_user
            ->get(route('manage.cp.playlists.create', $this->cp->id))
            ->assertSee(route('manage.cp.playlists.store', $this->cp->id))
            ->assertSee(__('manage/cp/contents/playlists.new_playlist'))
            ->assertSee('<button type="submit" class="btn btn-primary">'.__('common.save').'</button>')
            ->assertDontSee('<button type="button" class="btn btn-primary" onclick="submitForm()">'.__('common.save_and_submit').'</button>')
            ->assertStatus(200);
    }

    private function store_playlist_without_required_values_failed()
    {
        $this->acting_as_user
            ->post(route('manage.cp.playlists.store', $this->cp->id), [])
            ->assertSessionHasErrors([
                'name', 'genre', 'region', 'language',
            ]);

        $this->assertEquals(0, Playlist::all()->count());
    }

    private function store_playlist_with_all_required_values_success()
    {
        //content review feature on
        config(['features.content_review' => true]);

        //1.click save button
        $this->acting_as_user
            ->post(route('manage.cp.playlists.store', $this->cp->id), [
                'name' => 'Playlist',
                'genre' => 'food',
                'region' => 'CN',
                'language' => 'zh-Hans',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('playlists', [
            'name' => 'Playlist',
            'genre' => 'food',
            'region' => 'CN',
            'language' => 'zh-Hans',
            'status' => Playlist::STATUS_DRAFT,
        ]);

        $this->reset_empty_datas_for_playlist();

        //2.click save and submit button
        $this->acting_as_user
            ->post(route('manage.cp.playlists.store', $this->cp->id), [
                'name' => 'Playlist',
                'genre' => 'food',
                'region' => 'CN',
                'language' => 'zh-Hans',
                'is_submit' => 1,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('playlists', [
            'name' => 'Playlist',
            'genre' => 'food',
            'region' => 'CN',
            'language' => 'zh-Hans',
            'status' => Playlist::STATUS_PENDING,
        ]);

        $this->reset_empty_datas_for_playlist();

        //content review feature off, only click save button
        config(['features.content_review' => false]);

        $this->acting_as_user
            ->post(route('manage.cp.playlists.store', $this->cp->id), [
                'name' => 'Playlist',
                'genre' => 'food',
                'region' => 'CN',
                'language' => 'zh-Hans',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('playlists', [
            'name' => 'Playlist',
            'genre' => 'food',
            'region' => 'CN',
            'language' => 'zh-Hans',
            'status' => Playlist::STATUS_READY,
        ]);
    }

    private function edit_playlist()
    {
        $this->seed_playlist_datas();

        $faker = Factory::create();

        $this->playlist_pending->comment = $faker->text(100);
        $this->playlist_pending->save();

        //content review feature on
        config(['features.content_review' => true]);

        $this->acting_as_user
            ->get(route('manage.cp.playlists.edit', [$this->cp->id, $this->playlist_pending->id]))
            ->assertSee(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_pending->id]))
            ->assertSee(__('manage/cp/contents/playlists.edit_playlist'))
            ->assertSee(htmlspecialchars($this->playlist_pending->name, ENT_QUOTES))
            ->assertSee('<option value="'.$this->playlist_pending->genre.'" selected="selected">'.__('manage/cp/contents/playlists.'.$this->playlist_pending->genre).'</option>')
            ->assertSee('<option value="'.$this->playlist_pending->region.'" selected="selected">'.__('country.'.$this->playlist_pending->region).'</option>')
            ->assertSee('<option value="'.$this->playlist_pending->language.'" selected="selected">'.__('language.'.$this->playlist_pending->language).'</option>')
            ->assertSee(__('manage/cp/contents/playlists.review_comments'))
            ->assertSeeText(htmlspecialchars($this->playlist_pending->comment, ENT_QUOTES))
            ->assertSee('<button type="submit" class="btn btn-primary">'.__('common.save').'</button>')
            ->assertSee('<button type="button" class="btn btn-primary" onclick="submitForm()">'.__('common.save_and_submit').'</button>')
            ->assertStatus(200);

        //content review feature off
        config(['features.content_review' => false]);

        $this->acting_as_user
            ->get(route('manage.cp.playlists.edit', [$this->cp->id, $this->playlist_pending->id]))
            ->assertSee(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_pending->id]))
            ->assertSee(__('manage/cp/contents/playlists.edit_playlist'))
            ->assertSee(htmlspecialchars($this->playlist_pending->name, ENT_QUOTES))
            ->assertSee('<option value="'.$this->playlist_pending->genre.'" selected="selected">'.__('manage/cp/contents/playlists.'.$this->playlist_pending->genre).'</option>')
            ->assertSee('<option value="'.$this->playlist_pending->region.'" selected="selected">'.__('country.'.$this->playlist_pending->region).'</option>')
            ->assertSee('<option value="'.$this->playlist_pending->language.'" selected="selected">'.__('language.'.$this->playlist_pending->language).'</option>')
            ->assertDontSee(__('manage/cp/contents/playlists.review_comments'))
            ->assertDontSeeText(htmlspecialchars($this->playlist_pending->comment, ENT_QUOTES))
            ->assertSee('<button type="submit" class="btn btn-primary">'.__('common.save').'</button>')
            ->assertDontSee('<button type="button" class="btn btn-primary" onclick="submitForm()">'.__('common.save_and_submit').'</button>')
            ->assertStatus(200);

        $this->reset_empty_datas_for_playlist();
    }

    private function update_playlist_without_required_values_failed()
    {
        $this->seed_playlist_datas();

        $this->acting_as_user
            ->put(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_pending->id]), [
                'name' => null,
                'genre' => null,
                'region' => null,
                'language' => null,
            ])
            ->assertSessionHasErrors([
                'name', 'genre', 'region', 'language',
            ]);

        $playlist = $this->playlist_pending->fresh();
        $this->assertNotNull($playlist->name);
        $this->assertNotNull($playlist->genre);
        $this->assertNotNull($playlist->region);
        $this->assertNotNull($playlist->language);

        $this->reset_empty_datas_for_playlist();
    }

    private function update_playlist_with_all_required_values_success()
    {
        $this->seed_playlist_datas();

        //content review feature on
        config(['features.content_review' => true]);

        //1.click save button
        $this->acting_as_user
            ->put(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_approve->id]), [
                'name' => 'Playlist',
                'genre' => 'sports',
                'region' => 'US',
                'language' => 'en',
            ])
            ->assertSessionHas('success');

        $playlist = $this->playlist_approve->fresh();
        $this->assertEquals('Playlist', $playlist->name);
        $this->assertEquals('sports', $playlist->genre);
        $this->assertEquals('US', $playlist->region);
        $this->assertEquals('en', $playlist->language);
        $this->assertEquals(Playlist::STATUS_DRAFT, $playlist->status);

        //2.click save and submit button
        $this->acting_as_user
            ->put(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_approve->id]), [
                'name' => 'Playlist',
                'genre' => 'sports',
                'region' => 'US',
                'language' => 'en',
                'is_submit' => 1,
            ])
            ->assertSessionHas('success');

        $playlist = $this->playlist_approve->fresh();
        $this->assertEquals('Playlist', $playlist->name);
        $this->assertEquals('sports', $playlist->genre);
        $this->assertEquals('US', $playlist->region);
        $this->assertEquals('en', $playlist->language);
        $this->assertEquals(Playlist::STATUS_PENDING, $playlist->status);

        //content review feature off, only click save button
        config(['features.content_review' => false]);

        $this->acting_as_user
            ->put(route('manage.cp.playlists.update', [$this->cp->id, $this->playlist_approve->id]), [
                'name' => 'Playlist',
                'genre' => 'sports',
                'region' => 'US',
                'language' => 'en',
            ])
            ->assertSessionHas('success');

        $playlist = $this->playlist_approve->fresh();
        $this->assertEquals('Playlist', $playlist->name);
        $this->assertEquals('sports', $playlist->genre);
        $this->assertEquals('US', $playlist->region);
        $this->assertEquals('en', $playlist->language);
        $this->assertEquals(Playlist::STATUS_READY, $playlist->status);

        $this->reset_empty_datas_for_playlist();
    }

    private function publish_playlist()
    {
        $ready_playlist = factory(Playlist::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
            'status' => Playlist::STATUS_READY,
        ]);

        $this->acting_as_user
            ->get(route('manage.cp.playlists.publish', [$this->cp->id, $ready_playlist->id]))
            ->assertStatus(200);

        //don't publish playlist to marketplace
        $this->acting_as_user
            ->put(route('manage.cp.playlists.update-publish', [$this->cp->id, $ready_playlist->id]), [
                'radio_publish' => 'off',
            ])
            ->assertSessionHas('success', __('manage/cp/contents/playlists.playlist_is_unpublished_successfully'));

        $playlist = $ready_playlist->fresh();
        $this->assertNull($playlist->publish_start_date);
        $this->assertNull($playlist->publish_end_date);
        $this->assertNull($playlist->tom_id);
        $this->assertFalse($playlist->publish);
        $this->assertEquals(Playlist::PUBLISH_STATUS_UNPUBLISH, $playlist->publish_status);

        //publish playlist to marketplace
        $tom = factory(TermsOfMarketplace::class)->create([
            'property_id' => $this->cp->id,
            'user_id' => $this->content_uploader->id,
        ]);

        //1.click save button
        $this->acting_as_user
            ->put(route('manage.cp.playlists.update-publish', [$this->cp->id, $ready_playlist->id]), [
                'radio_publish' => 'on',
                'marketplace_terms' => $tom->id,
                'publish_start_date' => Carbon::now(),
                'publish_end_date' => Carbon::now()->addDays(7),
            ])
            ->assertSessionHas('success', __('manage/cp/contents/playlists.playlist_is_published_successfully'));

        $playlist = $ready_playlist->fresh();
        $this->assertNotNull($playlist->publish_start_date);
        $this->assertNotNull($playlist->publish_end_date);
        $this->assertEquals($tom->id, $playlist->tom_id);
        $this->assertTrue($playlist->publish);
        $this->assertEquals(Playlist::PUBLISH_STATUS_UNPUBLISH, $playlist->publish_status);

        //2.click save and submit button
        $this->acting_as_user
            ->put(route('manage.cp.playlists.update-publish', [$this->cp->id, $ready_playlist->id]), [
                'radio_publish' => 'on',
                'marketplace_terms' => $tom->id,
                'available_now' => 1,
                'until_forever' => 1,
                'is_submit' => true,
            ])
            ->assertSessionHas('success', __('manage/cp/contents/playlists.playlist_is_published_successfully'));

        $playlist = $ready_playlist->fresh();
        $this->assertNull($playlist->publish_start_date);
        $this->assertNull($playlist->publish_end_date);
        $this->assertEquals($tom->id, $playlist->tom_id);
        $this->assertTrue($playlist->publish);
        $this->assertEquals(Playlist::PUBLISH_STATUS_REVIEW, $playlist->publish_status);

        $this->reset_empty_datas_for_playlist();
    }

    private function can_request_review_when_content_review_feature_on()
    {
        config(['features.content_review' => true]);

        $this->seed_playlist_datas();

        $this->acting_as_user
            ->get(route('manage.cp.playlists.index', $this->cp->id))
            ->assertSee('<li><a href="javascript:void(0)" onclick="bulk_action(0);">'.__('manage/cp/contents/playlists.request_review').'</a></li>');

        $playlist_ids = Playlist::pluck('id')->toArray();

        $this->acting_as_user
            ->post(route('manage.cp.playlists.review.request', $this->cp->id), [
                'playlist_ids' => implode(',', $playlist_ids),
            ])
            ->assertSessionHas('success', __('manage/cp/contents/playlists.send_playlist_review_requests_successfully'));

        $draft_playlist = $this->playlist_draft->fresh();
        $this->assertEquals(Playlist::STATUS_PENDING, $draft_playlist->status);

        $ready_playlist = $this->playlist_approve->fresh();
        $this->assertNotEquals(Playlist::STATUS_PENDING, $ready_playlist->status);

        $reject_playlist = $this->playlist_reject->fresh();
        $this->assertNotEquals(Playlist::STATUS_PENDING, $reject_playlist->status);

        $this->reset_empty_datas_for_playlist();
    }

    private function cannot_request_review_when_content_review_feature_off()
    {
        config(['features.content_review' => false]);

        $this->acting_as_user
            ->get(route('manage.cp.playlists.index', $this->cp->id))
            ->assertDontSee('<li><a href="javascript:void(0)" onclick="bulk_action(0);">'.__('manage/cp/contents/playlists.request_review').'</a></li>');
    }

    private function delete_playlist()
    {
        $this->seed_playlist_datas();

        $playlist_ids = Playlist::pluck('id')->toArray();

        $this->acting_as_user
            ->delete(route('manage.cp.playlists.destroy', [$this->cp->id, $this->playlist_draft->id]), [
                'playlist_id' => implode(',', $playlist_ids),
            ])
            ->assertSessionHas('success', __('manage/cp/contents/playlists.playlists_are_destroyed'));

        $this->reset_empty_datas_for_playlist();
    }
}
