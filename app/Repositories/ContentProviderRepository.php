<?php

namespace App\Repositories;

use App\Models\PropertyCP;

class ContentProviderRepository
{
    /**
     * @var PropertyCP
     */
    private $contentProviderProperty;

    public function __construct(PropertyCP $contentProviderProperty)
    {
        $this->contentProviderProperty = $contentProviderProperty;
    }

    /**
     * Find content provider property by api_token and api_key.
     *
     * @param  $key
     * @param  $token
     *
     * @return PropertyCP
     */
    public function findContentProviderByKeyAndToken($key, $token)
    {
        return  $this->contentProviderProperty->where('api_key', $key)
            ->where('api_token', $token)
            ->first();
    }
}
