<?php

namespace App\Services\Vod\Ali;

class UrlService
{
    const RESIZE_TYPE_LFIT = 'lfit';        //等比缩放
    const RESIZE_TYPE_FIXED = 'fixed';       //强制缩略
    const RESIZE_TYPE_FILL = 'fill';        //裁剪

    /**
     * @param $fileUrl
     * @param $options ['width' => '', 'height' => '', 'resize_type' => ]
     * OssClient::OSS_PROCESS => "image/resize,m_lfit,h_100,w_100",
     *
     * @return string
     */
    public static function getUrl($fileUrl, $options = [])
    {
        if (empty($fileUrl)) {
            return null;
        }
        $options = self::getImageHandleParams($options);

        return $fileUrl.(empty($options) ? '' : '?x-oss-process='.$options);
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
