<?php

use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPlaylistRequestReviewViewDatas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('drop view if exists entry_playlist_request_review_views;');
        DB::statement('
            create or replace view entry_playlist_request_review_view_datas as 
                    select 
                        id, user_id, property_id, name, id as entry_id, null as playlist_id, \'Entry\' as type, status, image_path, created_at 
                    from 
                        entries 
                    where 
                        status in (\''.Entry::STATUS_PENDING.'\',\''.Entry::STATUS_REJECTED.'\') and deleted_at is null
                union
                    select 
                        id, user_id, property_id, name, null as entry_id, id as playlist_id, \'Playlist\' as type, status, thumbnail_path as image_path, created_at 
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
