<?php

use App\Models\User;
use App\Models\PropertyCP;
use App\Models\RequestLog;

$factory->define(RequestLog::class, function (Faker\Generator $faker) {
    return [
        'cp_property_id' => factory(PropertyCP::class)->create()->id,
        'requester_user_id' => factory(User::class)->create()->id,
        'subject' => $faker->sentence,
        'message' => $faker->text,
    ];
});
