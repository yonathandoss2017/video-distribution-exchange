<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MarketplaceTerm extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'region_allowed' => $this->transformToLang($this->region_allowed, 'region.', $request),
            'region_excepted' => $this->transformToLang($this->region_excepted, 'region.', $request),
            'payment_mode' => $this->payment_mode,
            'payment_mode_in_lang' => __('term.payment_mode.'.$this->payment_mode, [], $request->get('locale')),
            'exclusivity' => $this->exclusivity,
            'price' => $this->price,
            'update_count' => $this->update_count,
            'revenue_share_cp' => $this->revenue_share_cp,
            'revenue_share_sp' => $this->revenue_share_sp,
            'payment_comments' => $this->payment_comments,
            'api_share_to' => $this->transformToLang($this->api_share_to, 'term.api_share_to.', $request),
            'download_resolution' => $this->transformToLang($this->download_resolution, 'term.video_download.', $request),
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }

    private function transformToLang($arr, $langPath, $request)
    {
        if ($arr) {
            foreach ($arr as $item) {
                $resArr[] = __($langPath.$item, [], $request->get('locale'));
            }

            return implode(', ', $resArr);
        } else {
            return null;
        }
    }
}
