<?php

return [
    'default' => (in_array(env('APP_ENV'), ['production', 'staging', 'feature', 'local']) ? env('APP_ENV') : 'staging'),

    'marketplace' => [
        'endpoint' => [
            'production' => [
                'scheme' => env('SOLR_SCHEME', 'http'),
                'host' => env('SOLR_HOST', '106.14.145.52'),
                'port' => env('SOLR_PORT', '8983'),
                'path' => env('SOLR_PATH', '/solr/'),
                'core' => env('SOLR_CORE', 'ivx-china'),
                'timeout' => env('SOLR_TIMEOUT', 30),
                'username' => env('SOLR_USERNAME', null),
                'password' => env('SOLR_PASSWORD', null),
                'key' => 'production',
            ],
            'staging' => [
                'scheme' => env('SOLR_SCHEME', 'http'),
                'host' => env('SOLR_HOST', 'ivx-search-dev.ivideocloud.cn'),
                'port' => env('SOLR_PORT', '8983'),
                'path' => env('SOLR_PATH', '/solr/'),
                'core' => env('SOLR_CORE', 'ivx-china-dev'),
                'timeout' => env('SOLR_TIMEOUT', 30),
                'username' => env('SOLR_USERNAME', null),
                'password' => env('SOLR_PASSWORD', null),
                'key' => 'staging',
            ],
            'feature' => [
                'scheme' => env('SOLR_SCHEME', 'http'),
                'host' => env('SOLR_HOST', 'localhost'),
                'port' => env('SOLR_PORT', '8983'),
                'path' => env('SOLR_PATH', '/solr/'),
                'core' => env('SOLR_CORE', 'ivx_v2_dev_2'),
                'timeout' => env('SOLR_TIMEOUT', 30),
                'username' => env('SOLR_USERNAME', null),
                'password' => env('SOLR_PASSWORD', null),
                'key' => 'feature',
            ],
            'local' => [
                'scheme' => env('SOLR_SCHEME', 'http'),
                'host' => env('SOLR_HOST', 'localhost'),
                'port' => env('SOLR_PORT', '8983'),
                'path' => env('SOLR_PATH', '/solr/'),
                'core' => env('SOLR_CORE', 'ivx-china-local'),
                'timeout' => env('SOLR_TIMEOUT', 30),
                'username' => env('SOLR_USERNAME', null),
                'password' => env('SOLR_PASSWORD', null),
                'key' => 'local',
            ],
        ],
        'minimum_match' => '75%',
    ],
];
