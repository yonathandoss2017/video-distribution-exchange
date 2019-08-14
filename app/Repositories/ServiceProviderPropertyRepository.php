<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\PropertySP;

class ServiceProviderPropertyRepository
{
    /**
     * @var PropertySP
     */
    private $serviceProviderProperty;

    public function __construct(PropertySP $serviceProviderProperty)
    {
        $this->serviceProviderProperty = $serviceProviderProperty;
    }

    /**
     * Find service provider property by api_token,api_key.
     *
     * @param  $key
     * @param  $token
     *
     * @return PropertySP
     */
    public function findServiceProviderByKeyToken($key, $token)
    {
        return  $this->serviceProviderProperty->where('api_key', $key)
            ->where('api_token', $token)
            ->first();
    }
}
