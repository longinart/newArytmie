<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Arytmie Praha</title>
        <meta
            name="description"
            content="Ženský komorní sbor Arytmie Praha. Přehled aktualit, koncertů, galerie a kontaktů na jednom místě."
        >

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-stone-50 text-stone-900 antialiased">
        <header class="border-b border-orange-100 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Arytmie Praha</p>
                    <p class="text-sm text-stone-600">Ženský komorní sbor</p>
                </div>

                <nav class="hidden gap-6 text-sm font-medium text-stone-700 md:flex">
                    <a href="#o-sboru" class="transition hover:text-orange-500">O sboru</a>
                    <a href="#aktuality" class="transition hover:text-orange-500">Aktuality</a>
                    <a href="#koncerty" class="transition hover:text-orange-500">Koncerty</a>
                    <a href="{{ route('gallery.index') }}" class="transition hover:text-orange-500">Galerie</a>
                    <a href="#kontakt" class="transition hover:text-orange-500">Kontakt</a>
                </nav>
            </div>
        </header>

        @php
            $heroSiteDir = public_path('images/site');
            $heroDesktopRel = null;
            foreach (['hero-desktop.jpg', 'hero-desktop.png'] as $heroDesktopFile) {
                if (file_exists($heroSiteDir.DIRECTORY_SEPARATOR.$heroDesktopFile)) {
                    $heroDesktopRel = 'images/site/'.$heroDesktopFile;
                    break;
                }
            }
            $heroMobileRel = null;
            foreach (['hero-mobile.png', 'hero-mobile.jpg', 'arytmie_mobile.png'] as $heroMobileFile) {
                if (file_exists($heroSiteDir.DIRECTORY_SEPARATOR.$heroMobileFile)) {
                    $heroMobileRel = 'images/site/'.$heroMobileFile;
                    break;
                }
            }
            $hasHeroDesktop = $heroDesktopRel !== null;
            $hasHeroMobile = $heroMobileRel !== null;
        @endphp

        @if ($hasHeroDesktop || $hasHeroMobile)
            <div class="w-full bg-stone-200">
                <picture>
                    @if ($hasHeroMobile)
                        <source media="(max-width: 768px)" srcset="{{ asset($heroMobileRel) }}" />
                    @endif
                    <img
                        src="{{ asset($hasHeroDesktop ? $heroDesktopRel : $heroMobileRel) }}"
                        alt="Ženský komorní sbor Arytmie Praha"
                        class="block h-auto w-full object-cover object-center"
                        width="{{ $hasHeroDesktop ? '2560' : '800' }}"
                        height="{{ $hasHeroDesktop ? '400' : '200' }}"
                        @if ($hasHeroDesktop)
                            fetchpriority="high"
                        @endif
                        decoding="async"
                    />
                </picture>
            </div>
        @endif

        <main>
            <section class="bg-gradient-to-b from-stone-200/80 to-stone-50">
                <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8 lg:py-24">
                    <div class="space-y-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Nový web ve výstavbě</p>
                        <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">
                            Hudba, která spojuje ženské hlasy, prostor a emoci.
                        </h1>
                        <p class="max-w-2xl text-lg leading-8 text-stone-700">
                            Připravujeme nový web ženského komorního sboru Arytmie Praha s přehlednými aktualitami,
                            kalendářem koncertů, galerií a jednoduchou administrací pro správu obsahu.
                        </p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#koncerty"
                                class="rounded-full bg-orange-500 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-orange-600"
                            >
                                Zobrazit koncerty
                            </a>
                            <a
                                href="#kontakt"
                                class="rounded-full border border-orange-200 bg-white px-6 py-3 text-center text-sm font-semibold text-orange-600 transition hover:border-orange-300 hover:bg-orange-50"
                            >
                                Kontaktovat sbor
                            </a>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-orange-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-500">Co už je připraveno</p>
                        <ul class="mt-4 space-y-3 text-sm text-stone-700">
                            <li class="rounded-2xl bg-orange-50 px-4 py-3">Laravel 13 + Livewire 4 běží na `newarytmie.test`.</li>
                            <li class="rounded-2xl bg-orange-50 px-4 py-3">Databáze je přepnutá na MySQL v Laragonu.</li>
                            <li class="rounded-2xl bg-orange-50 px-4 py-3">Hotový je základ datového modelu pro obsah, koncerty i galerii.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="o-sboru" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">O sboru</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight">Komorní ženský sbor s prostorem pro detail</h2>
                    </div>
                    <div class="space-y-4 text-stone-700">
                        <p>
                            Tato úvodní verze webu slouží jako základ pro budoucí prezentaci sboru, jeho historie,
                            repertoáru a koncertní činnosti. Obsah bude navázán na editovatelnou administraci.
                        </p>
                        <p>
                            Cílem je jednoduchá správa textů, novinek, koncertů a fotografií bez potřeby zasahovat do kódu.
                        </p>
                    </div>
                </div>
            </section>

            <section id="aktuality" class="bg-white">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="flex items-end justify-between gap-6">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Aktuality</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight">Novinky spravované z administrace</h2>
                        </div>
                        <p class="hidden max-w-md text-sm text-stone-600 md:block">
                            Tady budou postupně přibývat články a oznámení načítané z databáze.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-6 md:grid-cols-3">
                        @forelse ($newsItems as $news)
                            <article class="rounded-3xl border border-stone-200 bg-stone-50 p-6">
                                <p class="text-sm text-orange-500">
                                    {{ $news->published_at?->format('d.m.Y') ?? 'Aktualita' }}
                                </p>
                                <h3 class="mt-2 text-xl font-semibold">
                                    <a href="{{ route('news.show', $news->slug) }}" class="hover:text-orange-500">
                                        {{ $news->title }}
                                    </a>
                                </h3>
                                <p class="mt-3 text-sm leading-6 text-stone-700">
                                    {{ \Illuminate\Support\Str::limit($news->excerpt ?: strip_tags($news->content), 140) }}
                                </p>
                                <a href="{{ route('news.show', $news->slug) }}" class="mt-4 inline-block text-sm font-semibold text-orange-500 hover:text-orange-600">
                                    Cist aktualitu &rarr;
                                </a>
                            </article>
                        @empty
                            <article class="rounded-3xl border border-stone-200 bg-stone-50 p-6 md:col-span-3">
                                <p class="text-sm text-orange-500">Zatím bez příspěvků</p>
                                <h3 class="mt-2 text-xl font-semibold">Aktuality se brzy objeví</h3>
                                <p class="mt-3 text-sm leading-6 text-stone-700">
                                    Jakmile v administraci publikujete první článek, zobrazí se automaticky tady.
                                </p>
                            </article>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="koncerty" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="rounded-[2rem] bg-stone-900 px-6 py-8 text-white lg:px-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Koncerty</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight">Nejbližší koncerty</h2>
                    <div class="mt-8 grid gap-4 md:grid-cols-3">
                        @forelse ($concertItems as $concert)
                            <div class="rounded-3xl bg-white/10 p-5">
                                <p class="text-sm text-orange-200">{{ $concert->starts_at?->format('d.m.Y H:i') }}</p>
                                <p class="mt-2 text-lg font-semibold">
                                    <a href="{{ route('concerts.show', $concert->slug) }}" class="hover:text-orange-200">
                                        {{ $concert->title }}
                                    </a>
                                </p>
                                <p class="mt-2 text-sm text-orange-100">{{ $concert->venue_name }}, {{ $concert->city }}</p>
                                @if ($concert->program)
                                    <p class="mt-2 text-xs text-orange-100">
                                        {{ \Illuminate\Support\Str::limit($concert->program, 90) }}
                                    </p>
                                @endif
                                <a href="{{ route('concerts.show', $concert->slug) }}" class="mt-3 inline-block text-sm font-semibold text-orange-200 hover:text-white">
                                    Detail koncertu &rarr;
                                </a>
                            </div>
                        @empty
                            <div class="rounded-3xl bg-white/10 p-5 md:col-span-3">
                                <p class="text-sm text-orange-200">Zatím bez termínů</p>
                                <p class="mt-2 text-lg font-semibold">Koncertní kalendář se právě připravuje</p>
                                <p class="mt-2 text-sm text-orange-100">
                                    Po publikaci koncertu v administraci se data zobrazí automaticky.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="galerie" class="bg-white">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Galerie</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight">Fotografie budou rozdělené do alb</h2>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="aspect-square rounded-3xl bg-orange-100"></div>
                        <div class="aspect-square rounded-3xl bg-orange-200"></div>
                        <div class="aspect-square rounded-3xl bg-stone-200"></div>
                        <div class="aspect-square rounded-3xl bg-stone-300"></div>
                    </div>
                    <p class="mt-6 max-w-2xl text-sm leading-6 text-stone-700">
                        Datový model už počítá s alby i samostatnými fotografiemi, včetně pořadí, popisků a publikačního stavu.
                    </p>
                    <a href="{{ route('gallery.index') }}" class="mt-6 inline-block text-sm font-semibold text-orange-500 hover:text-orange-600">
                        Otevrit galerii &rarr;
                    </a>
                </div>
            </section>

            <section id="kontakt" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Kontakt</p>
                        <h2 class="text-3xl font-semibold tracking-tight">Spojte se s námi</h2>
                        <p class="text-stone-700">
                            Kontaktní formulář bude ukládat zprávy do administrace. V další etapě doplním validaci,
                            CSRF ochranu, odeslání e-mailu a správu přijatých zpráv.
                        </p>
                    </div>

                    <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <form class="grid gap-4">
                            <input type="text" placeholder="Jméno" class="rounded-2xl border border-stone-300 px-4 py-3 outline-none transition focus:border-orange-400">
                            <input type="email" placeholder="E-mail" class="rounded-2xl border border-stone-300 px-4 py-3 outline-none transition focus:border-orange-400">
                            <input type="text" placeholder="Předmět" class="rounded-2xl border border-stone-300 px-4 py-3 outline-none transition focus:border-orange-400">
                            <textarea rows="5" placeholder="Vaše zpráva" class="rounded-2xl border border-stone-300 px-4 py-3 outline-none transition focus:border-orange-400"></textarea>
                            <button type="button" class="rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white">
                                Formulář doplním v další etapě
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
