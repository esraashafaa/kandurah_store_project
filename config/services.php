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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'stripe' => [
        'public' => env('STRIPE_PUBLIC'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'firebase' => [
        // مسار ملف Service Account من Firebase (أي اسم للملف). إن كان نسبياً يُحسب من storage/app
        'credentials_path' => (function () {
            $path = env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase-credentials.json'));
            if ($path && !realpath($path) && !str_contains($path, DIRECTORY_SEPARATOR) === false) {
                $fromStorage = storage_path('app/' . basename($path));
                if (file_exists($fromStorage)) {
                    return $fromStorage;
                }
            }
            return $path;
        })(),
        'project_id' => env('FIREBASE_PROJECT_ID', 'kandoura-f5fb2'),
    ],

];
