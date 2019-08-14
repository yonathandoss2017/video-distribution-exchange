<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EntrySubtitle extends Resource
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
            'lang' => $this->lang,
            'lang_name' => $this->language_name,
            'url' => \App\Services\Storage\Oss\UrlService::getUrl($this->url),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
