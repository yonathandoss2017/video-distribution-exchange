<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    protected $fillable = ['code', 'name'];

    public function scopeByCode($query, $code)
    {
        return $query->where('code', '=', $code);
    }
}
