<?php

use App\Models\PlatformOauth;

$factory->define(PlatformOauth::class, function (Faker\Generator $faker) {
    return [
        'platform' => 'youtube',
    ];
});
