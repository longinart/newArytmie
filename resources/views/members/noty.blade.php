@extends('layouts.members')

@section('title', 'Noty | Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Noty</h1>
            <p class="mt-2 text-sm text-slate-400">
                Partitury a PDF — jen pro členky po zadání hesla na vstupu do sekce.
            </p>

            @include('members.partials.subnav')

            <article class="rounded-2xl border border-slate-600/80 bg-slate-900/40 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-white">Soubory a odkazy</h2>
                <p class="mt-2 text-sm leading-6 text-slate-300">
                    Sem patří odkazy na PDF (např. ve sdíleném úložišti) nebo seznam skladeb k tisku.
                    Obsah zatím doplňte v šabloně
                    <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs text-orange-200">resources/views/members/noty.blade.php</code>
                    nebo přes budoucí administraci.
                </p>
            </article>
        </div>
    </section>
@endsection
