@extends('layouts.members')

@section('title', 'Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Členská sekce</h1>
            <p class="mt-4 text-sm leading-6 text-slate-300">
                Oblast zatím není aktivní — na serveru chybí nastavení hesla v konfiguraci
                (<span class="font-mono text-slate-400">MEMBERS_AREA_PASSWORD</span> v souboru prostředí).
            </p>
            <p class="mt-4 text-sm text-slate-500">
                O nastavení požádejte správce webu.
            </p>
        </div>
    </section>
@endsection
