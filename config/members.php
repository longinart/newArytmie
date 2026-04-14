<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Členská sekce (sdílené heslo)
    |--------------------------------------------------------------------------
    |
    | Jednoduchý přístup bez uživatelských účtů. Heslo nastavte v .env
    | (MEMBERS_AREA_PASSWORD). Bez hesla sekce zobrazí hlášku místo formuláře.
    |
    */

    'enabled' => (bool) env('MEMBERS_AREA_ENABLED', true),

    'password' => env('MEMBERS_AREA_PASSWORD'),

    /*
    | Kalendář zkoušek / vnitřní harmonogram (jen členská sekce).
    */
    'rehearsal_calendar' => [
        'embed_src' => env('MEMBERS_GOOGLE_CALENDAR_EMBED_SRC'),
        'ical_url' => env('MEMBERS_GOOGLE_CALENDAR_ICAL_URL'),
        'timezone' => env('MEMBERS_GOOGLE_CALENDAR_TIMEZONE', 'Europe/Prague'),
        'mode' => env('MEMBERS_GOOGLE_CALENDAR_MODE', 'AGENDA'),
    ],

];
