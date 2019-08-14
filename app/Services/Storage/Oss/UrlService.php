<?php

namespace App\Services\Storage\Oss;

use OSS\OssClient;

class UrlService
{
    const RESIZE_TYPE_LFIT = 'lfit';        //等比缩放
    const RESIZE_TYPE_FIXED = 'fixed';       //强制缩略
    const RESIZE_TYPE_FILL = 'fill';        //裁剪

    /**
     * @param $filePath
     * @param $options ['width' => '', 'height' => '', 'resize_type' => ]
     * @param $publicAccess
     * OssClient::OSS_PROCESS => "image/resize,m_lfit,h_100,w_100",
     *
     * @return string
     */
    public static function getUrl($filePath, $options = [], $publicAccess = true, $internalUse = false)
    {
        if (empty($filePath)) {
            return null;
        }

        $endpoint = config('oss.cname_endpoint');
        $isCName = true;
        if ($internalUse) {
            $endpoint = config('oss.internal_endpoint');
            $isCName = false;
        }

        $oss = new AliOSS(config('oss.key_id'), config('oss.key_secret'), $endpoint, $isCName);
        $oss->setBucket(config('oss.bucket'));
        $options = self::getImageHandleParams($options);
        if ($publicAccess) {
            if ($oss->doesObjectExist($filePath)) {
                $url = $oss->getHost().'/'.$filePath;

                return $url.(empty($options) ? '' : '?x-oss-process='.$options);
            }
        } else {
            if ($oss->doesObjectExist($filePath)) {
                if (!empty($options)) {
                    $options = [
                        OssClient::OSS_PROCESS => $options,
                    ];
                }

                return $oss->signUrl($filePath, $options);
            }
        }

        return null;
    }

    private static function getImageHandleParams($options)
    {
        $params = empty($options) ? [] : ['image/resize'];
        if (isset($options['resize_type'])) {
            if (self::RESIZE_TYPE_LFIT == $options['resize_type']) {
                $params[] = 'm_'.self::RESIZE_TYPE_LFIT;
            } elseif (self::RESIZE_TYPE_FIXED == $options['resize_type']) {
                $params[] = 'm_'.self::RESIZE_TYPE_FIXED;
            } elseif (self::RESIZE_TYPE_FILL == $options['resize_type']) {
                $params[] = 'm_'.self::RESIZE_TYPE_FILL;
            }
        }
        if (!empty($options['height'])) {
            $params[] = 'h_'.$options['height'];
        }
        if (!empty($options['width'])) {
            $params[] = 'w_'.$options['width'];
        }

        return join(',', $params);
    }
}
