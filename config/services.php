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

    /*
    | Cloudflare Turnstile (kontaktní formulář)
    | https://developers.cloudflare.com/turnstile/
    */
    'turnstile' => [
        'enabled' => (bool) env('TURNSTILE_ENABLED', false),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    /*
    | Veřejný Google kalendář — embed na úvodní stránce (sekce Koncerty).
    | ID kalendáře najdete v Google Kalendář → ⚙ u kalendáře → Nastavení a sdílení → Integrace kalendáře.
    */
    'google_calendar' => [
        'embed_src' => env('GOOGLE_CALENDAR_EMBED_SRC'),
        'timezone' => env('GOOGLE_CALENDAR_TIMEZONE', 'Europe/Prague'),
        'mode' => env('GOOGLE_CALENDAR_MODE', 'AGENDA'),
    ],

];
