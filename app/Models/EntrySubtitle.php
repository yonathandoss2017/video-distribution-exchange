<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrySubtitle extends Model
{
    use SoftDeletes;

    const SUBTITLE_VTT = 'vtt';
    const SUBTITLE_SRT = 'srt';
    const SUPPORTED_SUBTITLES = [self::SUBTITLE_VTT, self::SUBTITLE_SRT];

    const MAX_SIZE = 512000; // in byte

    protected $dates = ['deleted_at'];

    protected $fillable = ['entry_id', 'lang', 'url'];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function getLanguageNameAttribute()
    {
        $lang_name = Language::where('code', $this->lang)->first();

        return trans('language.'.$lang_name->code);
    }
}
