<?php

use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\TermsOfDistribution;
use App\Models\DistributionRegionRight;

$factory->define(TermsOfDistribution::class, function (Faker\Generator $faker) {
    $cp = factory(PropertyCP::class)->create();

    return [
       'cp_property_id' => $cp->id,
       'cp_organization_id' => $cp->organization_id,
       'sp_property_id' => factory(PropertySP::class)->create()->id,
       'status' => $faker->randomElement(TermsOfDistribution::STATUSES),
       'name' => $faker->name,
       'internal_remarks' => $faker->text(50),
       'published_at' => $faker->time('Y-m-d H:i:s'),
       'cp_deleted_at' => null,
       'sp_deleted_at' => null,
       'creator' => function () {
           return factory(\App\Models\User::class)->create()->id;
       },
       'updater' => function () {
           return factory(\App\Models\User::class)->create()->id;
       },
       'show_new_mark' => 0,
       'deleted_at' => null,
   ];
});

$factory->define(DistributionRegionRight::class, function (Faker\Generator $faker) {
    return [
       'tod_id' => function () {
           $tod = TermsOfDistribution::inRandomOrder()->doesntHave('regionRights')->first();

           return object_get($tod, 'id') ?: factory(TermsOfDistribution::class)->create()->id;
       },
       'allowed_regions' => $faker->randomElement(['014,018,142', '015,002,011', '019,030,035']),
       'excepted_regions' => $faker->randomElement(['BI', null]),
       'started_at' => $faker->dateTimeThisMonth(),
       'ended_at' => $faker->dateTimeThisMonth(),
       'payment_mode' => 'charge-download',
       'exclusivity' => 'non-exclusive',
       'price' => $faker->randomNumber(2),
       'update_count' => $faker->randomNumber(2),
       'revenue_share_cp' => $faker->randomNumber(2),
       'revenue_share_sp' => $faker->randomNumber(2),
       'api_share_to' => 'qq',
       'download_resolution' => 'hd',
       'extra_terms' => $faker->words(3, true),
   ];
});
