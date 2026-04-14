@extends('layouts.members')

@section('title', 'Náslechy | Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Náslechy</h1>
            <p class="mt-2 text-sm text-slate-400">
                Materiály a poznámky k poslechu / přípravě — obsah může doplnit vedení sboru.
            </p>

            @include('members.partials.subnav')

            <article class="rounded-2xl border border-slate-600/80 bg-slate-900/40 p-6 shadow-sm">
                <p class="text-sm leading-6 text-slate-300">
                    Tato stránka je připravená pro odkazy na nahrávky, odkazy na streamy nebo krátké pokyny k domácímu poslechu.
                    Text a odkazy zatím doplňte ručně v šabloně
                    <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs text-orange-200">resources/views/members/naslechy.blade.php</code>,
                    případně později přes administraci.
                </p>
            </article>
        </div>
    </section>
@endsection
