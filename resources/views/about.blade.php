@extends('layouts.public')

@section('title', 'O nás | Arytmie Praha')

@push('meta')
    <meta
        name="description"
        content="Ženský komorní sbor Arytmie Praha: o sboru, repertoáru, složení a členství v Unii českých pěveckých sborů."
    >
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 via-slate-800 to-slate-900">
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">O nás</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                Arytmie Praha
            </h1>

            <figure class="mt-10 border-l-4 border-orange-400 pl-6">
                <blockquote class="text-lg leading-8 text-slate-200 sm:text-xl">
                    <p>„Kdo chce pochopit hudbu, nepotřebuje ani tak sluch, jako srdce.“</p>
                </blockquote>
                <figcaption class="mt-4 text-sm font-medium text-orange-300">Jiří Mahen</figcaption>
            </figure>

            <div class="mt-12 space-y-6 text-base leading-7 text-slate-200">
                <p>
                    Ženský komorní sbor <strong class="font-semibold text-white">Arytmie Praha</strong> vznikl z touhy zpívat
                    náročnější repertoár v menším obsazení a pečovat o komorní zvuk i výraz. Scházíme se pravidelně,
                    připravujeme koncerty a rádi objevujeme skladby od renesance přes romantiku až po současné autory.
                </p>
                <p>
                    Uměleckou vedoucí je <strong class="font-semibold text-white">Lenka Menclová</strong>. Repertoár vybíráme
                    tak, aby odpovídal možnostem sboru a zároveň posouval zpěvačky dál — ať už jde o polyfonii, intonaci,
                    nebo společnou dynamiku v menším počtu hlasů.
                </p>
            </div>

            <div class="mt-14">
                <h2 class="text-xl font-semibold tracking-tight text-white">Aktuálně zpíváme</h2>
                <p class="mt-3 text-sm text-slate-400">
                    Složení se může měnit podle koncertního období; orientační rozložení hlasů:
                </p>
                <ul class="mt-6 space-y-2 text-slate-200">
                    <li class="flex gap-3">
                        <span class="shrink-0 font-mono text-sm text-orange-400">1.</span>
                        <span>1. soprán</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 font-mono text-sm text-orange-400">2.</span>
                        <span>2. soprán</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 font-mono text-sm text-orange-400">3.</span>
                        <span>1. alt</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 font-mono text-sm text-orange-400">4.</span>
                        <span>2. alt</span>
                    </li>
                </ul>
            </div>

            <p class="mt-14 text-sm leading-6 text-slate-400">
                Sbor je členem
                <a
                    href="https://www.ucps.cz/"
                    class="font-medium text-orange-400 underline decoration-orange-400/40 underline-offset-2 transition hover:text-orange-300"
                    target="_blank"
                    rel="noopener noreferrer"
                >Unie českých pěveckých sborů (UČPS)</a>.
            </p>

            <p class="mt-10">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center text-sm font-semibold text-orange-400 transition hover:text-orange-300"
                >
                    &larr; Zpět na úvod
                </a>
            </p>
        </div>
    </section>
@endsection
