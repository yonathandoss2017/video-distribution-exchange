<?php

namespace App\Models;

class ContentProviderProperty extends PropertyCP
{
    public function __construct(array $attributes = [])
    {
        if ('production' !== env('APP_ENV')) {
            echo 'You are using a legacy PropertyCP Model. Please change to ProperyCP.';
        }
        parent::__construct($attributes);
    }
}
