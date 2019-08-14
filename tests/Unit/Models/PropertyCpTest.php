<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use App\Models\Entry;
use Tests\BaseTestCase;
use App\Models\Playlist;
use App\Models\PropertyCP;
use App\Models\PlatformOauth;
use App\Models\PropertyContent;
use App\Models\LicenseNotification;
use App\Models\TermsOfDistribution;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PropertyCpTest extends BaseTestCase
{
    use RefreshDatabase;

    protected $cpSmall;                     //create 10 entries
    protected $cpLarge;                     //create 100 entries

    public function setUp()
    {
        parent::setUp();
        $this->seedBase();
        $this->loginAsSuperAdmin();
        $this->init_cp_and_related_datas($this->cpSmall, 10);
        $this->init_cp_and_related_datas($this->cpLarge, 100);
    }

    private function init_cp_and_related_datas(&$cp, $entryNum)
    {
        $cp = factory(PropertyCP::class)->create();

        for ($i = 0; $i < $entryNum; ++$i) {
            factory(Entry::class)->create([
                'user_id' => $this->super_admin->id,
                'property_id' => $cp->id,
            ]);
        }

        factory(Playlist::class, 2)->create([
            'property_id' => $cp->id,
        ]);

        factory(LicenseNotification::class, 2)->create([
            'property_id' => $cp->id,
        ]);

        factory(PlatformOauth::class, 2)->create([
            'property_id' => $cp->id,
        ]);

        factory(PropertyContent::class)->create([
            'property_id' => $cp->id,
        ]);

        factory(TermsOfDistribution::class, 2)->create([
            'cp_property_id' => $cp->id,
            'cp_deleted_at' => Carbon::now(),
        ]);
    }

    /**
     * @test
     */
    public function test_delete()
    {
        $this->assert_delete_small_cp_and_related_data_success();
        $this->assert_delete_large_cp_and_related_data_success();
    }

    private function assert_delete_small_cp_and_related_data_success()
    {
        $this->assertDatabaseHas('properties', PropertyCP::find($this->cpSmall->id)->toArray());

        $this->cpSmall->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', $entries->toArray());
        });

        $this->cpSmall->playlists->map(function ($playlists) {
            $this->assertDatabaseHas('playlists', $playlists->toArray());
        });

        $this->cpSmall->notifications->map(function ($license_notifications) {
            $this->assertDatabaseHas('license_notifications', $license_notifications->toArray());
        });

        $this->cpSmall->oauth->map(function ($oauth) {
            $this->assertDatabaseHas('platform_oauth', $oauth->toArray());
        });

        $this->assertDatabaseHas('property_contents', $this->cpSmall->propertyContent->toArray());

        $this->cpSmall->todsWithTrashed->map(function ($tods) {
            $this->assertDatabaseHas('terms_of_distributions', $tods->toArray());
        });

        $this->cpSmall->delete();

        $deletedProperty = PropertyCP::withTrashed()->find($this->cpSmall->id)->makeVisible(['api_key', 'api_token']);

        $this->check_cp_related_data_deleted($deletedProperty, 'small');
    }

    private function assert_delete_large_cp_and_related_data_success()
    {
        $this->assertDatabaseHas('properties', PropertyCP::find($this->cpLarge->id)->toArray());

        $this->cpLarge->entries->map(function ($entries) {
            $this->assertDatabaseHas('entries', $entries->toArray());
        });

        $this->cpLarge->playlists->map(function ($playlists) {
            $this->assertDatabaseHas('playlists', $playlists->toArray());
        });

        $this->cpLarge->notifications->map(function ($license_notifications) {
            $this->assertDatabaseHas('license_notifications', $license_notifications->toArray());
        });

        $this->cpLarge->oauth->map(function ($oauth) {
            $this->assertDatabaseHas('platform_oauth', $oauth->toArray());
        });

        $this->assertDatabaseHas('property_contents', $this->cpLarge->propertyContent->toArray());

        $this->cpLarge->todsWithTrashed->map(function ($tods) {
            $this->assertDatabaseHas('terms_of_distributions', $tods->toArray());
        });

        $this->cpLarge->delete();

        $deletedProperty = PropertyCP::withTrashed()->find($this->cpLarge->id)->makeVisible(['api_key', 'api_token']);

        $this->check_cp_related_data_deleted($deletedProperty, 'large');
    }

    private function check_cp_related_data_deleted($deletedProperty, $type)
    {
        $this->assertSoftDeleted('properties', $deletedProperty->toArray());

        $deletedProperty->entries()->withTrashed()->get()->map(function ($entries) {
            $this->assertSoftDeleted('entries', $entries->toArray());
        });

        $deletedProperty->playlists()->withTrashed()->get()->map(function ($playlists) {
            $this->assertSoftDeleted('playlists', $playlists->toArray());
        });

        $deletedProperty->notifications()->withTrashed()->get()->map(function ($license_notifications) {
            $this->assertSoftDeleted('license_notifications', $license_notifications->toArray());
        });

        $deletedProperty->oauth()->get()->map(function ($oauth) {
            $this->assertSoftDeleted('platform_oauth', $oauth->toArray());
        });

        $this->assertSoftDeleted('property_contents', $deletedProperty->propertyContent()->withTrashed()->first()->toArray());

        $deletedProperty->todsWithTrashed()->withTrashed()->get()->map(function ($tods) {
            $this->assertSoftDeleted('terms_of_distributions', $tods->toArray());
        });
    }
}
