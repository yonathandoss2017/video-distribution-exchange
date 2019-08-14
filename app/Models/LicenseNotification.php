<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseNotification extends Model
{
    use SoftDeletes;

    const ENABLE = 1;
    const DISABLE = 0;

    protected $fillable = [
        'user_id', 'property_id', 'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
