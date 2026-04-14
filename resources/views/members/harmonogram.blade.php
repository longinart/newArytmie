@extends('layouts.members')

@section('title', 'Harmonogram | Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Harmonogram</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-400">
                Společný kalendář sboru (zkoušky a domluvené akce). Stejná data můžete přidat do telefonu přes odkaz iCal níže.
            </p>

            @include('members.partials.subnav')

            @if (! empty($membersCalendarEmbedUrl))
                <div class="overflow-hidden rounded-2xl border border-slate-600/80 bg-white shadow-lg">
                    <iframe
                        class="h-[min(720px,75vh)] w-full border-0"
                        src="{{ $membersCalendarEmbedUrl }}"
                        title="Harmonogram sboru — Google Calendar"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                @if (filled($membersCalendarIcalUrl))
                    <p class="mt-4 text-sm text-slate-400">
                        <a
                            href="{{ $membersCalendarIcalUrl }}"
                            class="font-medium text-orange-400 underline-offset-2 hover:text-orange-300 hover:underline"
                            rel="noopener noreferrer"
                        >
                            Otevřít / přidat kalendář (iCal)
                        </a>
                        <span class="text-slate-500"> — většina aplikací umí import z URL.</span>
                    </p>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-slate-600 bg-slate-900/40 p-8 text-center text-sm text-slate-400">
                    Kalendář zatím není v konfiguraci serveru nastavený. Požádejte správce webu o doplnění
                    <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs text-orange-200">MEMBERS_GOOGLE_CALENDAR_EMBED_SRC</code>.
                </div>
            @endif
        </div>
    </section>
@endsection
