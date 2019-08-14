<?php

use App\Models\User;
use App\Models\Entry;
use App\Models\Property;
use App\Models\EntryMeta;
use App\Models\EntryAnalytic;
use App\Models\EntrySubtitle;
use App\Models\EntryLocalization;

$factory->define(Entry::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(3),
        'description' => $faker->paragraph(5),
        'media_type' => 'video',
        'duration' => $faker->randomDigit,
        'status' => Entry::STATUS_READY,
        'published' => true,
        'published_at' => Carbon\Carbon::now()->subDay(),
        'platforms' => 'alivod',
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
    ];
});

$factory->define(EntryMeta::class, function (Faker\Generator $faker) {
    return [
        'tags' => $faker->word,
        'video_type' => 'movie_feature',
        'director' => $faker->firstNameMale,
        'stars' => $faker->firstNameMale,
        'genre' => 'animals',
        'region' => $faker->country,
        'privacy' => 'public',
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
    ];
});

$factory->define(EntryLocalization::class, function (Faker\Generator $faker) {
    return [
        'lang' => $faker->languageCode ?? null,
        'title' => $faker->title,
        'description' => $faker->sentence,
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
    ];
});

$factory->define(EntryAnalytic::class, function (Faker\Generator $faker) {
    return [
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
        'property_id' => function () {
            return factory(Property::class)->create()->id;
        },
        'views' => $faker->randomNumber(),
    ];
});

$factory->define(EntrySubtitle::class, function (Faker\Generator $faker) {
    return [
        'entry_id' => function () {
            return factory(Entry::class)->create()->id;
        },
        'lang' => $faker->unique()->languageCode,
        'url' => $faker->url,
    ];
});
