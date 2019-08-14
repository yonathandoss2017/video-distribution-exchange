<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryAnalytic extends Model
{
    use SoftDeletes;

    protected $table = 'entry_analytics';
    protected $dates = ['deleted_at'];
    protected $fillable = ['entry_id', 'property_id', 'views'];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function property()
    {
        return $this->belongsTo(PropertySP::class, 'property_id');
    }
}
