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

    'firebase' => [
        'url' => env('FIREBASE_URL'),
        'secret' => env('FIREBASE_SECRET'),
    ],

    'gopos' => [
        'base_url' => env('GOPOS_BASE_URL', 'https://app.gopos.io'),
        'client_id' => env('GOPOS_CLIENT_ID'),
        'client_secret' => env('GOPOS_CLIENT_SECRET'),
        'organization_id' => env('GOPOS_ORGANIZATION_ID'),
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-5-5'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        'version' => '2023-06-01',
        'timeout' => (int) env('ANTHROPIC_TIMEOUT', 60),
        // Model może zużyć część limitu na wewnętrzne rozumowanie, więc zapas musi być duży.
        'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 4096),

        // Cena w USD za milion tokenów (claude-sonnet-5-5: 2 / 10). Tokeny myślenia liczone są w output.
        'price_input_per_mtok' => (float) env('ANTHROPIC_PRICE_INPUT_PER_MTOK', 2.0),
        'price_output_per_mtok' => (float) env('ANTHROPIC_PRICE_OUTPUT_PER_MTOK', 10.0),
    ],
];
