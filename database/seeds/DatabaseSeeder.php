<?php

use App\Models\GeoRegion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!$this->command->confirm('It will seed data to database "'.config('database.connections.'.config('database.default').'.database').'". Do you wish to continue?')) {
            $this->command->info('Recommended to create new fresh database.');
            exit;
        }

        DB::statement('SET sql_mode=NO_AUTO_VALUE_ON_ZERO');
        Schema::disableForeignKeyConstraints();
        $this->truncateTable();
        Schema::enableForeignKeyConstraints();

        Model::unguard();

        $this->seedGeoRegionData();

        $this->call(UsersTableSeeder::class);
        $this->call(OrganizationsTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        if (!App::environment(['production', 'staging'])) {
            $this->call(PlaylistsTableSeeder::class);
            $this->call(EntriesTableSeeder::class);
            $this->call(EntryMetasTableSeeder::class);
            $this->call(EntryPlaylistTableSeeder::class);
            $this->call(AddMarketplaceTermToCpSeeder::class);
            $this->call(EntryAnalyzesTableSeeder::class);
        }

        Model::reguard();

        $this->displayInformation();
    }

    protected function truncateTable()
    {
        DB::table('organizations')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_user')->truncate();
        DB::table('users')->truncate();
        DB::table('properties')->truncate();
        DB::table('license_notifications')->truncate();

        DB::table('entries')->truncate();
        DB::table('entry_playlist')->truncate();
        DB::table('entry_property')->truncate();
        DB::table('entry_metas')->truncate();
        DB::table('platform_dailymotions')->truncate();
        DB::table('platform_hungamas')->truncate();
        DB::table('platform_ivideostreams')->truncate();
        DB::table('platform_livestreams')->truncate();
        DB::table('platform_youtubes')->truncate();
        DB::table('platform_kalturas')->truncate();

        DB::table('playlist_property')->truncate();
        DB::table('playlist_terms_of_distribution')->truncate();
        DB::table('distribution_region_rights')->truncate();
        DB::table('playlists')->truncate();
        DB::table('terms_of_distributions')->truncate();
        DB::table('property_metas')->truncate();
        DB::table('languages')->truncate();
        DB::table('entry_analyzes')->truncate();
    }

    protected function seedGeoRegionData()
    {
        if (0 == GeoRegion::count()) {
            $path = database_path('seeds/data/GeoRegionData.sql');
            DB::unprepared(file_get_contents($path));

            $this->command->info('geo_region data seeded'.PHP_EOL);
        }
    }

    protected function displayInformation()
    {
        $this->command->info(PHP_EOL.'Below the generated information: ');
        $headers = ['Name', 'Count'];
        $rows = [
            [
                'name' => 'Users',
                'count' => \App\Models\User::count(),
            ],
            [
                'name' => 'Organization',
                'count' => \App\Models\Organization::count(),
            ],
            [
                'name' => 'Property CP',
                'count' => \App\Models\PropertyCP::count(),
            ],
            [
                'name' => 'Property SP',
                'count' => \App\Models\PropertySP::count(),
            ],
        ];
        $this->command->table($headers, $rows);
    }
}
