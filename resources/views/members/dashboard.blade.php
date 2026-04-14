@extends('layouts.members')

@section('title', 'Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Nácviky a noty</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        Materiály jen pro členky sboru. Obsah může doplnit administrátorka webu.
                    </p>
                </div>
                <form method="post" action="{{ route('members.lock') }}" class="shrink-0">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-full border border-slate-500 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-orange-400 hover:text-white"
                    >
                        Zavřít sekci
                    </button>
                </form>
            </div>

            <div class="mt-12 space-y-10">
                <article class="rounded-2xl border border-slate-600/80 bg-slate-900/40 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white">Nácviky</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Zde můžete později uvést termíny zkoušek, místnost, změny programu nebo odkazy na kalendář.
                        Zatím je sekce připravená jako rozcestník — konkrétní text doplníte podle potřeby (nebo přes administraci, až ji napojíme).
                    </p>
                    <ul class="mt-4 list-disc space-y-2 pl-5 text-sm text-slate-400">
                        <li>Pravidelná zkouška — doplňte den a čas.</li>
                        <li>Místo — doplňte adresu nebo sál.</li>
                    </ul>
                </article>

                <article class="rounded-2xl border border-slate-600/80 bg-slate-900/40 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-white">Noty a partitury</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">
                        Sem patří odkazy na PDF v úložišti, Google Drive pro sbor, nebo seznam skladeb k procvičení.
                        Dokumenty můžete hostovat např. v <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs text-orange-200">storage/app/public</code> a odkazovat přes dočasné nebo chráněné URL podle vašeho nastavení.
                    </p>
                    <ul class="mt-4 list-disc space-y-2 pl-5 text-sm text-slate-400">
                        <li>Aktuální program koncertu — PDF.</li>
                        <li>Domácí úkoly / nahrávky — odkazy.</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>
@endsection
