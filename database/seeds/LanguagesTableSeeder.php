<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        \DB::table('languages')->delete();

        \DB::table('languages')->insert([
            0 => [
                'id' => 1,
                'code' => 'de',
                'name' => 'German',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:43',
            ],
            1 => [
                'id' => 2,
                'code' => 'en',
                'name' => 'English',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 15:16:25',
            ],
            2 => [
                'id' => 3,
                'code' => 'es',
                'name' => 'Spanish',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 15:16:25',
            ],
            3 => [
                'id' => 4,
                'code' => 'fr',
                'name' => 'French',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:43',
            ],
            4 => [
                'id' => 5,
                'code' => 'it',
                'name' => 'Italian',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:43',
            ],
            5 => [
                'id' => 6,
                'code' => 'pt',
                'name' => 'Portuguese',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 15:16:25',
            ],
            6 => [
                'id' => 7,
                'code' => 'vi',
                'name' => 'Vietnamese',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            7 => [
                'id' => 8,
                'code' => 'tr',
                'name' => 'Turkish',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            8 => [
                'id' => 9,
                'code' => 'ru',
                'name' => 'Russian',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            9 => [
                'id' => 10,
                'code' => 'ar',
                'name' => 'Arabic',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            10 => [
                'id' => 11,
                'code' => 'hi',
                'name' => 'Hindi',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            11 => [
                'id' => 12,
                'code' => 'zh-Hans',
                'name' => 'Chinese (Simplified)',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 15:16:25',
            ],
            12 => [
                'id' => 13,
                'code' => 'zh-Hant',
                'name' => 'Chinese (Traditional)',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 15:16:25',
            ],
            13 => [
                'id' => 14,
                'code' => 'ja',
                'name' => 'Japanese',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
            14 => [
                'id' => 15,
                'code' => 'ko',
                'name' => 'Korean',
                'created_at' => '2017-01-03 13:59:38',
                'updated_at' => '2017-10-24 11:52:44',
            ],
        ]);
    }
}
