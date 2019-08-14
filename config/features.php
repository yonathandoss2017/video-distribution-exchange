<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Approve Mode
    |--------------------------------------------------------------------------
    |
    | When your application set .env -- APP_APPROVE=true, new video will need
    | approve.
    |
     */

    'content_review' => env('FEATURE_CONTENT_REVIEW', false),

    'log_view' => env('FEATURE_LOG_VIEW', false),
];
