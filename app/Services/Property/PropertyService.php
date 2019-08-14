<?php

namespace App\Services\Property;

use Illuminate\Support\Facades\Hash;

class PropertyService
{
    public static function generateKeyAndToken($property)
    {
        if (!$property->api_key) {
            $property->api_key = md5($property->organization_id.$property->id.$property->created_at);
        }
        //make sure that the generated token is different with existing one.
        do {
            $apiToken = md5(Hash::make(str_random(16)));
        } while ($apiToken == $property->api_token);
        $property->api_token = $apiToken;
        $property->save();
    }
}
