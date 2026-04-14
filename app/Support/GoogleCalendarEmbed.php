<?php

namespace App\Support;

class GoogleCalendarEmbed
{
    /**
     * URL pro iframe „Vložit kalendář“ (veřejný kalendář).
     * Calendar ID: Nastavení kalendáře → Integrace kalendáře → ID kalendáře.
     */
    public static function embedUrl(): ?string
    {
        $src = config('services.google_calendar.embed_src');
        if (! is_string($src) || trim($src) === '') {
            return null;
        }

        $query = array_filter([
            'src' => $src,
            'ctz' => config('services.google_calendar.timezone') ?: 'Europe/Prague',
            'mode' => config('services.google_calendar.mode') ?: 'AGENDA',
            'showTitle' => '0',
            'showNav' => '1',
            'showDate' => '1',
            'showPrint' => '0',
            'showTabs' => '1',
            'showCalendars' => '0',
            'showTz' => '0',
        ], static fn ($v) => $v !== null && $v !== '');

        return 'https://calendar.google.com/calendar/embed?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
