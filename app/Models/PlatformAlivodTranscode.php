<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformAlivodTranscode extends Model
{
    use SoftDeletes;

    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_NORMAL = 'Normal';

    /*
     * definition
     * 视频流清晰度定义, 取值：FD(流畅)，LD(标清)，SD(高清)，HD(超清)，OD(原画)，2K(2K)，4K(4K).
     */

    /**
     * get the platform_alivod that owns the platform_alivod_transcode.
     */
    public function platformAlivod()
    {
        return $this->belongsTo(PlatformAlivod::class);
    }
}
