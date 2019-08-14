<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/31
 * Time: 16:38.
 */

return [
    'key_id' => env('ALIVOD_KEY_ID'),
    'key_secret' => env('ALIVOD_KEY_SECRET'),
    'region_id' => env('ALIVOD_REGION_ID'),
    'transcode_templates' => [
        'standard' => env('ALIVOD_STANDARD_TEMPLATE_ID'),
    ],
    'ai_review_template' => env('ALIVOD_AI_REVIEW_TEMPLATE_ID'),
    'endpoint' => env('ALIVOD_ENDPOINT'),
    'cname_endpoint' => env('ALIVOD_CNAME_ENDPOINT'),
];
