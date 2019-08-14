<?php

namespace Tests;

use Exception;
use TestUserSeeder;
use App\Models\User;
use TestPropertySeeder;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Exceptions\Handler;
use TestOrganizationSeeder;
use App\Models\Organization;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;
use Illuminate\Contracts\Debug\ExceptionHandler;

abstract class BaseTestCase extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function followRedirects($response)
    {
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * @param $key
     * @param $message
     */
    protected function assertHasMessageInSession($key, $message)
    {
        $hasError = session()->has('errors');
        $this->assertEquals($hasError, true);
        if ($hasError) {
            $messageBag = session()->get('errors');
            $this->assertEquals($messageBag->has($key), true);
            $this->assertEquals($messageBag->first($key), $message);
        }
    }

    /**
     * Instantiate the current testcase class handler exception into ExceptionHandler class,
     * and then capture and output exception information in testcase.
     */
    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class() extends Handler {
            public function __construct()
            {
            }

            public function report(\Exception $e)
            {
            }

            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });
    }

    /**
     * create org datas by seed.
     */
    protected function seedOrg()
    {
        \Artisan::call('db:seed', ['--class' => 'TestOrganizationSeeder']);

        $this->organization = Organization::find(TestOrganizationSeeder::ID_ORGANIZATION_1);
    }

    /**
     * create property datas by seed.
     */
    protected function seedProperty()
    {
        \Artisan::call('db:seed', ['--class' => 'TestPropertySeeder']);

        $this->cp = PropertyCP::find(TestPropertySeeder::ID_CP_1);
        $this->sp = PropertySP::find(TestPropertySeeder::ID_SP_1);
    }

    /**
     * create user datas by seed.
     */
    protected function seedUser()
    {
        \Artisan::call('db:seed', ['--class' => 'TestRoleSeeder']);
        \Artisan::call('db:seed', ['--class' => 'TestUserSeeder']);

        $this->super_admin = User::find(TestUserSeeder::ID_USER_SUPER_ADMIN);
        $this->org_admin = User::find(TestUserSeeder::ID_USER_ADMIN_1);
        $this->cp_manager = User::find(TestUserSeeder::ID_USER_CP_1);
        $this->sp_manager = User::find(TestUserSeeder::ID_USER_SP_1);
        $this->content_uploader = User::find(TestUserSeeder::ID_USER_CONTENT_UPLOADER);
        $this->censor = User::find(TestUserSeeder::ID_USER_CENSOR);
    }

    /**
     * create org and property and user datas by seed
     * default login as normal manager.
     *
     * @param $role user role
     */
    protected function seedBaseAndLoginAsManager($role = 'cp')
    {
        \Artisan::call('db:seed', ['--class' => 'TestBaseSeeder']);

        $this->super_admin = User::find(TestUserSeeder::ID_USER_SUPER_ADMIN);
        $this->org_admin = User::find(TestUserSeeder::ID_USER_ADMIN_1);
        $this->cp_manager = User::find(TestUserSeeder::ID_USER_CP_1);
        $this->sp_manager = User::find(TestUserSeeder::ID_USER_SP_1);
        $this->organization = Organization::find(TestOrganizationSeeder::ID_ORGANIZATION_1);
        $this->cp = PropertyCP::find(TestPropertySeeder::ID_CP_1);
        $this->sp = PropertySP::find(TestPropertySeeder::ID_SP_1);

        if (Property::TYPE_CP == $role) {
            $this->loginAsManager($this->cp_manager);
        } elseif (Property::TYPE_SP == $role) {
            $this->loginAsManager($this->sp_manager);
        }
    }

    /**
     * create org and property and user base datas by seed.
     */
    protected function seedBase()
    {
        $this->seedOrg();
        $this->seedProperty();
        $this->seedUser();
    }

    /**
     * Act as super admin, login as organization and go to Add User screen for redirect->back.
     */
    protected function loginAsSuperAdmin()
    {
        $this->actingAs($this->super_admin)->withSession(['organization' => $this->organization->id]);
    }

    /**
     * default login as normal manager.
     *
     * @param $role user role
     */
    protected function seedTodBaseAndLoginAsManager($role = 'cp')
    {
        \Artisan::call('db:seed', ['--class' => 'TestBaseSeeder']);

        $this->organization = Organization::find(TestOrganizationSeeder::ID_ORGANIZATION_1);
        $this->cp_manager = User::find(TestUserSeeder::ID_USER_CP_1);
        $this->sp_manager = User::find(TestUserSeeder::ID_USER_SP_1);
        $this->cp = PropertyCP::find(TestPropertySeeder::ID_CP_1);
        $this->sp = PropertySP::find(TestPropertySeeder::ID_SP_1);

        $playlist = factory(Playlist::class)->create();

        $this->tod = factory(TermsOfDistribution::class)->create([
            'cp_property_id' => $this->cp->id,
            'sp_property_id' => $this->sp->id,
            'status' => TermsOfDistribution::STATUS_DRAFT,
        ]);

        $region = factory(DistributionRegionRight::class)->create([
            'tod_id' => $this->tod->id,
        ]);

        $this->tod->playlists()->attach($playlist->id);

        if (Property::TYPE_CP == $role) {
            $this->loginAsManager($this->cp_manager);
        } elseif (Property::TYPE_SP == $role) {
            $this->loginAsManager($this->sp_manager);
        }
    }

    /**
     * create countries datas by seed.
     */
    protected function seedCountries()
    {
        \Artisan::call('db:seed', ['--class' => 'CountriesTableSeeder']);
    }

    /**
     * create languages datas by seed.
     */
    protected function seedLanguages()
    {
        \Artisan::call('db:seed', ['--class' => 'LanguagesTableSeeder']);
    }

    protected function loginAsManager($role = 'cp')
    {
        if (Property::TYPE_CP == $role) {
            $this->actingAs($this->cp_manager)->withSession(['organization' => $this->organization->id]);
        } elseif (Property::TYPE_SP == $role) {
            $this->actingAs($this->sp_manager)->withSession(['organization' => $this->organization->id]);
        }
    }
}
