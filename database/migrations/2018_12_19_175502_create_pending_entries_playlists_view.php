<?php

use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Database\Migrations\Migration;

class CreatePendingEntriesPlaylistsView extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('
            create or replace view pending_entries_playlists as 
                    select 
                        id, user_id, property_id, name, \'Entry\' as type, image_path, created_at 
                    from 
                        entries 
                    where 
                        status = \''.Entry::STATUS_PENDING.'\' and deleted_at is null
                union 
                    select 
                        id, user_id, property_id, name, \'Playlist\' as type, thumbnail_path as image_path, created_at 
                    from 
                        playlists 
                    where 
                        status = '.Playlist::STATUS_PENDING.' and deleted_at is null;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('drop view pending_entries_playlists;');
    }
}
