<?php

namespace App\Models;

class ServiceProviderProperty extends PropertySP
{
    public function __construct(array $attributes = [])
    {
        if ('production' !== env('APP_ENV')) {
            echo 'You are using a legacy PropertySP Model. Please change to ProperySP.';
        }
        parent::__construct($attributes);
    }
}
