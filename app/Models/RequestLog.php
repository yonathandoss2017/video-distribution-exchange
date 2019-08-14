<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestLog extends Model
{
    use SoftDeletes;
    protected $table = 'request_logs';

    protected $fillable = [
        'requester_user_id',
        'subject',
        'message',
        'read_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'request_log_user')->withPivot(['name', 'email']);
    }

    public function serviceProviders()
    {
        return $this->belongsToMany(Property::class, 'request_log_property')->where('properties.type', Property::TYPE_SP);
    }

    public function contentProviders()
    {
        return $this->belongsToMany(Property::class, 'request_log_property')->where('properties.type', Property::TYPE_CP);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'request_log_playlist');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getRecipientEmailsAttribute()
    {
        return implode(', ', $this->recipients->pluck('email')->toArray());
    }

    public function scopeNotRead($query)
    {
        $query->where('read_at', null);
    }
}
