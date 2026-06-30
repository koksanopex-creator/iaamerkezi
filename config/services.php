<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'takvim' => [
        'url' => env('TAKVIM_URL', 'http://localhost:8001'),
    ],

    // === MICROSOFT GRAPH API (Mail Sistemi) ===
    'microsoft' => [
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'from_address' => env('MICROSOFT_FROM_ADDRESS', 'kys@koksan.com'),
    ],

    // === MERKEZİ SSO SİSTEMİ ===
    'central_sso' => [
        'url' => env('CENTRAL_SSO_URL', 'http://localhost:8001'),
        'app_code' => env('CENTRAL_SSO_APP_CODE', 'iaa-yonetim-sistemi'),
        'api_key' => env('CENTRAL_SSO_API_KEY'),
    ],

];
