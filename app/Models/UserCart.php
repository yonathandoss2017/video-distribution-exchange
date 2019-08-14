<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    protected $table = 'user_carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'playlist_id'];

    protected $dates = ['requested_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
