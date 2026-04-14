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

];
