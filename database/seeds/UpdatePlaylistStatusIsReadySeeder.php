<?php

use App\Models\Playlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePlaylistStatusIsReadySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('playlists')->update(['status' => Playlist::STATUS_READY]);
    }
}
