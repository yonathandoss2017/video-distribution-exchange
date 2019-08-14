<?php

use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPlaylistRequestReviewViews extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('drop view if exists pending_entries_playlists;');
        DB::statement('
            create or replace view entry_playlist_request_review_views as 
                    select 
                        id, user_id, property_id, name, \'Entry\' as type, status, image_path, created_at 
                    from 
                        entries 
                    where 
                        status in (\''.Entry::STATUS_PENDING.'\',\''.Entry::STATUS_REJECTED.'\') and deleted_at is null
                union 
                    select 
                        id, user_id, property_id, name, \'Playlist\' as type, status, thumbnail_path as image_path, created_at 
                    from 
                        playlists 
                    where 
                        status in ('.Playlist::STATUS_PENDING.','.Playlist::STATUS_REJECTED.') and deleted_at is null;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('drop view entry_playlist_request_review_views;');
    }
}
