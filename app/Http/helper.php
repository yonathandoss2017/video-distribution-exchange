<?php

use App\Models\Entry;
use App\Models\Language;
use Illuminate\Support\Facades\Route;

if (!function_exists('is_current_route')) {
    function is_current_route($routeName)
    {
        return Route::currentRouteName() == $routeName;
    }
}

if (!function_exists('route_active')) {
    function route_active($routeName)
    {
        if (is_current_route($routeName)) {
            return 'active';
        }

        return null;
    }
}

if (!function_exists('get_language_name')) {
    function get_language_name($code)
    {
        $language = Language::byCode($code)->first();
        if ($language) {
            return trans('language.'.$language->code);
        }

        return '-';
    }
}

if (!function_exists('convertTimeToSecond')) {
    /**
     * Convert time in H:i:s format to second.
     *
     * @param $time
     *
     * @return int
     */
    function convertTimeToSecond($time)
    {
        $seconds = strtotime("1970-01-01 $time UTC");

        return $seconds;
    }
}

if (!function_exists('is_meta_checked')) {
    /**
     * @param int  $checkingValue
     * @param null $currentValue
     *
     * @return bool
     */
    function is_meta_checked($checkingValue, $currentValue = null)
    {
        $currentValue = $currentValue ?? Entry::META_TYPE_NONE;

        return old('meta_type', $currentValue) == $checkingValue;
    }
}
