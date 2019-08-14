<?php

use Ramsey\Uuid\Uuid;
use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\Organization;
use App\Models\PropertyMeta;
use App\Models\PropertyEmbed;
use App\Models\PropertyContent;
use App\Models\PropertyMobiles;
use App\Models\PropertyAnalytic;

/*
 * Property CP
 */
$factory->define(Property::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'type' => Property::TYPE_CP,
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'api_key' => str_random(32),
        'api_token' => str_random(32),
        'allow_livestream' => rand(0, 1),
        'allow_ivideomobile' => rand(0, 1),
    ];
});

$factory->state(Property::class, 'superadmin', function (Faker\Generator $faker) {
    return [
        'id' => 0,
        'name' => 'For Admin',
        'organization_id' => Organization::ID_FOR_SUPER_ADMIN,
        'type' => Property::TYPE_SP_PLUS,
    ];
});

$factory->state(Property::class, 'cp', function (Faker\Generator $faker) {
    return [
        'type' => Property::TYPE_CP,
    ];
});

$factory->state(Property::class, 'sp', function (Faker\Generator $faker) {
    return [
        'uuid' => Uuid::uuid4(),
        'type' => Property::TYPE_SP,
    ];
});

$factory->define(PropertyCP::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'type' => Property::TYPE_CP,
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
    ];
});

$factory->define(PropertySP::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'type' => Property::TYPE_SP,
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
    ];
});

$factory->state(Property::class, 'ivwp', function (Faker\Generator $faker) {
});

$factory->state(Property::class, 'ivmb', function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'type' => Property::TYPE_SP,
        'allow_ivideomobile' => true,
    ];
});

$factory->define(PropertyAnalytic::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'name' => $faker->name,
        'site' => 1,
        'page' => $faker->url,
    ];
});

$factory->define(PropertyContent::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
    ];
});

$factory->define(PropertyMeta::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'meta_name' => $faker->name,
        'meta_value' => $faker->password(10),
    ];
});

$factory->define(PropertyMobiles::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'splashscreen_url' => $faker->imageUrl(),
    ];
});

$factory->define(PropertyEmbed::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'site_url' => $faker->url,
        'static_page_url' => $faker->url,
    ];
});
