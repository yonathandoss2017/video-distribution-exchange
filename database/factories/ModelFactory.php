<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

use App\Models\User;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\GeoRegion;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\Organization;
use App\Models\PlatformAlivod;
use App\Models\MarketplaceTerm;
use App\Models\PlaylistProperty;
use App\Models\TermsOfMarketplace;
use App\Models\AiReviewVideoResult;
use App\Models\EntryAiReviewResult;
use App\Models\LicenseNotification;
use App\Models\PlaylistEvidenceRequest;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'longid' => md5($faker->name),
        'remarks' => '',
        'activated_at' => Carbon\Carbon::now(),
    ];
});

/*
 * Organization
 */
$factory->define(Organization::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});

$factory->state(Organization::class, 'superadmin', function (Faker\Generator $faker) {
    Eloquent::unguard();

    return [
        'name' => 'For Superadmin',
    ];
});

$factory->define(Playlist::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(3),
        'description' => $faker->sentence(5),
        'property_id' => function () {
            return factory(PropertyCP::class)->create()->id;
        },
        'genre' => 'food',
        'region' => 'CN',
        'language' => 'zh-Hans',
    ];
});

$factory->define(PlaylistProperty::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(PropertySP::class)->create()->id;
        },
        'playlist_id' => function () {
            return factory(Playlist::class)->create()->id;
        },
        'status_bak' => 'approved',
        'cp_property_id' => function () {
            return factory(PropertyCP::class)->create()->id;
        },
        'playlist_name' => $faker->sentence(3),
        'thumbnail_path' => $faker->url,
    ];
});

$factory->define(LicenseNotification::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'status' => 1,
    ];
});

$factory->define(MarketplaceTerm::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'allowed_regions' => GeoRegion::REGION_GLOBAL_CODE,
        'revenue_share' => 'exclusive',
        'license_fee' => 'exclusive',
        'minimun_guarantee' => 'exclusive',
        'created_by' => function () {
            return factory(User::class)->create()->id;
        },
        'updated_by' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});

$factory->define(TermsOfMarketplace::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->name,
        'region_allowed' => GeoRegion::REGION_GLOBAL_CODE,
        'payment_mode' => 'charge-download',
    ];
});

$factory->define(PlatformAlivod::class, function (Faker\Generator $faker) {
    return [
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
        'status' => PlatformAlivod::STATUS_READY,
    ];
});

$factory->define(EntryAiReviewResult::class, function (Faker\Generator $faker) {
    return [
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
        'jobid' => $faker->uuid,
    ];
});

$factory->define(AiReviewVideoResult::class, function (Faker\Generator $faker) {
    return [
        'review_id' => function () {
            return factory(EntryAiReviewResult::class)->create()->id;
        },
    ];
});

$factory->define(PlaylistEvidenceRequest::class, function (Faker\Generator $faker) {
    return [
        'property_id' => function () {
            return factory(PropertyCP::class)->create()->id;
        },
        'playlist_id' => function () {
            return factory(Playlist::class)->create()->id;
        },
    ];
});
