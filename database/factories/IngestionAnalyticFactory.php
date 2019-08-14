<?php

use Carbon\Carbon;
use App\Models\IngestionAnalytic;

$factory->define(IngestionAnalytic::class, function (Faker\Generator $faker) {
    return [
        'date' => Carbon::now()->toDateString(),
        'youtube_success' => $faker->randomNumber(3),
        'youtube_failed' => $faker->randomNumber(3),
        'direct_success' => $faker->randomNumber(3),
        'direct_failed' => $faker->randomNumber(3),
        'rss_success' => $faker->randomNumber(3),
        'rss_failed' => $faker->randomNumber(3),
    ];
});
