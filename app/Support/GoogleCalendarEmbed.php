<?php

namespace App\Support;

class GoogleCalendarEmbed
{
    /**
     * URL pro iframe — kalendář na úvodní stránce (veřejný koncertní kalendář).
     */
    public static function embedUrl(): ?string
    {
        $src = config('services.google_calendar.embed_src');
        if (! is_string($src) || trim($src) === '') {
            return null;
        }

        return self::buildEmbedUrl(
            $src,
            config('services.google_calendar.timezone') ?: 'Europe/Prague',
            config('services.google_calendar.mode') ?: 'AGENDA',
        );
    }

    /**
     * URL pro iframe — kalendář v členské sekci (zkoušky / harmonogram).
     */
    public static function membersRehearsalEmbedUrl(): ?string
    {
        $src = config('members.rehearsal_calendar.embed_src');
        if (! is_string($src) || trim($src) === '') {
            return null;
        }

        return self::buildEmbedUrl(
            $src,
            config('members.rehearsal_calendar.timezone') ?: 'Europe/Prague',
            config('members.rehearsal_calendar.mode') ?: 'AGENDA',
        );
    }

    private static function buildEmbedUrl(string $src, string $timezone, string $mode): string
    {
        $query = array_filter([
            'src' => $src,
            'ctz' => $timezone,
            'mode' => $mode,
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
