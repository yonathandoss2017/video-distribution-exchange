<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryPlaylistRequestReviewViewData extends Model
{
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id');
    }
}
