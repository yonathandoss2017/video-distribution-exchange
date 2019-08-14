<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryLocalization extends Model
{
    use SoftDeletes;

    /**
     * The attributes that need transform to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Localization is not timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lang',
        'title',
        'description',
    ];

    /**
     * Relationship: metadata belong to an entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}
