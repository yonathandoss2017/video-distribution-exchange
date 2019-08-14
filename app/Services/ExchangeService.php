<?php

namespace App\Services;

use App\Models\Country;
use App\Models\GeoRegion;

class ExchangeService
{
    public static function validateRegion($request, $validator)
    {
        if ($request->allowed_regions && $request->excepted_regions) {
            $same = array_intersect($request->allowed_regions, $request->excepted_regions);
            if (count($same) > 0) {
                $validator->errors()->add('same_regions', __('manage/cp/exchange/marketplace_terms.same_should_not_be_selected'));

                return;
            }
        }
        if ($request->excepted_regions) {
            $excepted_regions = array_filter($request->excepted_regions, function ($v, $k) {
                return is_numeric($v);
            }, ARRAY_FILTER_USE_BOTH);

            $error = false;

            if (!empty($excepted_regions)) {
                if (in_array(GeoRegion::REGION_GLOBAL_CODE, $excepted_regions)) {
                    $error = true;
                } else {
                    $allowed_countries = Country::whereIn('region_code', $request->allowed_regions)->orWhereIn('sub_region_code', $request->allowed_regions)->orWhereIn('iso_3166_2', $request->allowed_regions)->pluck('iso_3166_2')->toArray();
                    foreach ($excepted_regions as $region) {
                        $excepted_countries = Country::where('region_code', $region)->orWhere('sub_region_code', $region)->pluck('iso_3166_2')->toArray();
                        $diff = array_diff($excepted_countries, $allowed_countries);
                        if (!empty($diff) && $diff != $excepted_countries) {
                            $error = true;
                            break;
                        }
                    }
                }
            }

            if ($error) {
                $validator->errors()->add('contain_regions', __('manage/cp/exchange/marketplace_terms.selected_in_except_should_not_selected_in_allowed'));

                return;
            }
        }
    }

    public static function validatePaymentMode($request, $validator)
    {
        if (in_array($request->payment_mode, ['revenue-share', 'charge-download', 'annual-download', 'monthly-download'])) {
            if (!$request->exclusivity) {
                $validator->errors()->add('exclusivity', __('manage/cp/exchange/marketplace_terms.exclusivity_required'));
            }
        }

        if ('revenue-share' == $request->payment_mode) {
            if (!is_numeric($request->revenue_share_cp) || $request->revenue_share_cp < 0) {
                $validator->errors()->add('revenue_share_cp', __('manage/cp/exchange/marketplace_terms.revenue_share_cp_required'));
            }
            if (!is_numeric($request->revenue_share_sp) || $request->revenue_share_sp < 0) {
                $validator->errors()->add('revenue_share_sp', __('manage/cp/exchange/marketplace_terms.revenue_share_sp_required'));
            }
            if (is_numeric($request->revenue_share_cp) && is_numeric($request->revenue_share_sp)) {
                if (100 != ($request->revenue_share_cp + $request->revenue_share_sp)) {
                    $validator->errors()->add('revenue_share_sp', __('manage/cp/exchange/marketplace_terms.revenue_share_must_equals_100'));
                }
            }
            if (!$request->api_share_to) {
                $validator->errors()->add('api_share_to', __('manage/cp/exchange/marketplace_terms.api_share_to_required'));
            }
        }

        if (in_array($request->payment_mode, ['charge-download', 'annual-download', 'monthly-download'])) {
            if (!is_numeric($request->price) || $request->price < 0) {
                $validator->errors()->add('price', __('manage/cp/exchange/marketplace_terms.price_required'));
            }
        }

        if (in_array($request->payment_mode, ['annual-download', 'monthly-download'])) {
            if (!is_numeric($request->update_count) || false !== strpos($request->update_count, '.') || $request->update_count < 1) {
                $validator->errors()->add('update_count', __('manage/cp/exchange/marketplace_terms.update_count_required'));
            }
        }

        if (in_array($request->payment_mode, ['charge-download', 'annual-download', 'monthly-download', 'free-download'])) {
            if (!$request->api_share_to && !$request->download_resolution) {
                $validator->errors()->add('api_share_to', __('manage/cp/exchange/marketplace_terms.distribution_model_at_least_choose_one'));
                $validator->errors()->add('download_resolution', __('manage/cp/exchange/marketplace_terms.distribution_model_at_least_choose_one'));
            }
        }

        if (1 == intval($request->save_to_marketplace_terms)) {
            if (!$request->tom_name) {
                $validator->errors()->add('tom_name', __('manage/cp/exchange/distribution.tom_name_required'));
            }
        }

        return;
    }

    public static function getPaymentDetail($request)
    {
        if (in_array($request->payment_mode, ['charge-download', 'annual-download', 'monthly-download'])) {
            $payment['price'] = $request->price;
        } else {
            $payment['price'] = null;
        }
        if (in_array($request->payment_mode, ['annual-download', 'monthly-download'])) {
            $payment['update_count'] = $request->update_count;
        } else {
            $payment['update_count'] = null;
        }
        if ('free-download' == $request->payment_mode) {
            $payment['exclusivity'] = null;
        } else {
            $payment['exclusivity'] = $request->exclusivity;
        }
        if ('revenue-share' == $request->payment_mode) {
            $payment['revenue_share_cp'] = $request->revenue_share_cp;
            $payment['revenue_share_sp'] = $request->revenue_share_sp;
            $payment['download_resolution'] = null;
        } else {
            $payment['revenue_share_cp'] = null;
            $payment['revenue_share_sp'] = null;
            if ($request->download_resolution) {
                $payment['download_resolution'] = implode(',', $request->download_resolution);
            } else {
                $payment['download_resolution'] = null;
            }
        }
        if ($request->api_share_to) {
            $payment['api_share_to'] = implode(',', $request->api_share_to);
        } else {
            $payment['api_share_to'] = null;
        }

        return $payment;
    }
}
