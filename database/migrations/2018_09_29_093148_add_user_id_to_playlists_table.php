<?php

use App\Models\User;
use App\Models\Playlist;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->integer('user_id')->default(0)->comment('user id')->after('id');
        });
        $user = User::where('email', 'admin@svc.com')->first();
        if ($user) {
            Playlist::where('id', '>', '0')->update(['user_id' => $user->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
