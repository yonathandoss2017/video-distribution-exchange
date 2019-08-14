<?php

use App\Models\PropertyCP;
use Illuminate\Database\Seeder;

class DeletePropertyDeletedPlaylistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        PropertyCP::with('playlists')->onlyTrashed()->get()->map(function ($cp) {
            $cp->playlists->map(function ($playlist) {
                $playlist->delete();
            });
        });
    }
}
