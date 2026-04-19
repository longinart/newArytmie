@extends('layouts.public')

@push('meta')
    <meta
        name="description"
        content="Ženský komorní sbor Arytmie Praha. Přehled aktualit, koncertů, galerie a kontaktů na jednom místě."
    >
@endpush

@section('content')
    <section class="bg-gradient-to-b from-slate-700 via-slate-800 to-slate-900">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8 lg:py-24">
            <div id="o-sboru" class="scroll-mt-24 space-y-6">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">O sboru</p>
                <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                    Komorní ženský sbor s prostorem pro detail
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-stone-100">
                    Ženský komorní sbor Arytmie Praha sdružuje zpěvačky, které rády objevují repertoár od renesance po současnost
                    a dbají na sjednocený zvuk i výraz v menším obsazení.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <a
                        href="#koncerty"
                        class="rounded-full bg-orange-500 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-orange-600"
                    >
                        Zobrazit koncerty
                    </a>
                    <a
                        href="#kontakt"
                        class="rounded-full border border-orange-300 bg-slate-800 px-6 py-3 text-center text-sm font-semibold text-orange-300 transition hover:border-orange-200 hover:bg-slate-700"
                    >
                        Kontaktovat sbor
                    </a>
                    <a
                        href="{{ route('about') }}"
                        class="text-center text-sm font-semibold text-orange-200 underline decoration-orange-400/50 underline-offset-4 transition hover:text-white sm:text-left"
                    >
                        Více o sboru &rarr;
                    </a>
                </div>
            </div>

            <aside class="rounded-3xl border border-orange-200/25 bg-slate-900/55 p-6 shadow-lg backdrop-blur-sm">
                @if ($concertItems->isNotEmpty())
                    @php($nextConcert = $concertItems->first())
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-300">Nejbližší koncert</p>
                    <p class="mt-4 text-sm text-orange-200/90">{{ $nextConcert->starts_at?->format('d.m.Y H:i') }}</p>
                    <p class="mt-2 text-lg font-semibold leading-snug text-white">
                        <a href="{{ route('concerts.show', $nextConcert->slug) }}" class="transition hover:text-orange-200">
                            {{ $nextConcert->title }}
                        </a>
                    </p>
                    <p class="mt-2 text-sm text-slate-300">{{ $nextConcert->venue_name }}, {{ $nextConcert->city }}</p>
                    <a
                        href="#koncerty"
                        class="mt-6 inline-block text-sm font-semibold text-orange-400 transition hover:text-orange-300"
                    >
                        Další termíny &rarr;
                    </a>
                @else
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-300">Z hudby</p>
                    <blockquote class="mt-4 border-l-4 border-orange-400/80 pl-4 text-base leading-7 text-slate-200">
                        <p>„Kdo chce pochopit hudbu, nepotřebuje ani tak sluch, jako srdce.“</p>
                        <footer class="mt-3 text-sm font-medium text-orange-200/90">Jiří Mahen</footer>
                    </blockquote>
                    <p class="mt-6 text-sm leading-6 text-slate-400">
                        Koncertní termíny průběžně doplňujeme — mrkněte do sekce Koncerty níže na stránce.
                    </p>
                    <a
                        href="#koncerty"
                        class="mt-4 inline-block text-sm font-semibold text-orange-400 transition hover:text-orange-300"
                    >
                        Přejít na koncerty &rarr;
                    </a>
                @endif
            </aside>
        </div>
    </section>

    <section id="aktuality" class="bg-slate-800">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-semibold tracking-tight text-orange-400 sm:text-5xl">Aktuality</h2>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @forelse ($newsItems as $news)
                    <article class="rounded-3xl border border-slate-600/80 bg-slate-900/50 p-6 shadow-sm">
                        <p class="text-sm text-orange-400">
                            {{ $news->published_at?->format('d.m.Y') ?? 'Aktualita' }}
                        </p>
                        <h3 class="mt-2 text-xl font-semibold text-white">
                            <a href="{{ route('news.show', $news->slug) }}" class="transition hover:text-orange-300">
                                {{ $news->title }}
                            </a>
                        </h3>
                        <p class="mt-3 text-sm leading-6 text-slate-300">
                            {{ \Illuminate\Support\Str::limit($news->excerpt ?: strip_tags(\Illuminate\Support\Str::markdown($news->content, ['html_input' => 'strip'])), 140) }}
                        </p>
                        <a href="{{ route('news.show', $news->slug) }}" class="mt-4 inline-block text-sm font-semibold text-orange-400 transition hover:text-orange-300">
                            Cist aktualitu &rarr;
                        </a>
                    </article>
                @empty
                    <article class="rounded-3xl border border-slate-600/80 bg-slate-900/50 p-6 shadow-sm md:col-span-3">
                        <p class="text-sm text-orange-400">Zatím bez příspěvků</p>
                        <h3 class="mt-2 text-xl font-semibold text-white">Aktuality se brzy objeví</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-300">
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

            @if (! empty($googleCalendarEmbedUrl))
                <div class="mt-10 border-t border-white/10 pt-10">
                    <p class="text-sm font-semibold text-orange-200">Kalendář koncertů</p>
                    <p class="mt-1 text-sm text-orange-100/90">
                        Oficiální termíny ve veřejném Google kalendáři (můžete si přidat do telefonu z nastavení kalendáře).
                    </p>
                    <div class="mt-4 overflow-hidden rounded-2xl border border-white/15 bg-white shadow-lg">
                        <iframe
                            class="h-[min(720px,75vh)] w-full border-0"
                            src="{{ $googleCalendarEmbedUrl }}"
                            title="Kalendář koncertů — Google Calendar"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section id="galerie" class="bg-slate-800">
        <div
            class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8"
            x-data="homeGalleryPeek(@js($galleryPreviewLightbox))"
            @keydown.window="handleKey($event)"
        >
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">Galerie</p>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white">Momentky z koncertů a zkoušek</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                Náhodný výběr z publikovaných fotek v albech — kliknutím zvětšíte náhled (jako v galerii). Kompletní alba otevřete odkazem níže.
            </p>
            <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($galleryPreviewPhotos as $photo)
                    <button
                        type="button"
                        class="group relative aspect-square w-full cursor-zoom-in overflow-hidden rounded-3xl border border-slate-600/80 bg-slate-900/50 text-left shadow-sm ring-0 transition hover:ring-2 hover:ring-orange-400/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
                        @click="openAt({{ $loop->index }})"
                        aria-label="Zvětšit fotografii"
                    >
                        <img
                            src="{{ route('gallery.photo.thumb', $photo) }}"
                            alt="{{ $photo->alt_text ?: ($photo->title ?: $photo->album->title) }}"
                            class="pointer-events-none h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            data-home-gallery-thumb
                            data-fallback-src="{{ Storage::disk('public')->url($photo->image_path) }}"
                            loading="lazy"
                            decoding="async"
                            width="320"
                            height="320"
                        >
                    </button>
                @endforeach
                @for ($g = $galleryPreviewPhotos->count(); $g < 4; $g++)
                    <div
                        class="flex aspect-square items-center justify-center rounded-3xl border border-dashed border-slate-600/50 bg-slate-900/20 px-3 text-center text-xs leading-snug text-slate-500"
                        aria-hidden="true"
                    >
                        Brzy přibude další fotka
                    </div>
                @endfor
            </div>
            <a href="{{ route('gallery.index') }}" class="mt-8 inline-block text-sm font-semibold text-orange-400 transition hover:text-orange-300">
                Otevřít celou galerii &rarr;
            </a>

            <template x-teleport="body">
                <template x-if="open">
                    <div
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8"
                        x-transition
                        role="dialog"
                        aria-modal="true"
                        aria-label="Zvětšená fotografie"
                    >
                        <div class="absolute inset-0 bg-black/90" @click="close()" aria-hidden="true"></div>

                        <button
                            type="button"
                            class="absolute right-2 top-2 z-[102] rounded-full border border-white/20 bg-slate-900/90 px-3 py-1.5 text-sm font-semibold text-white shadow transition hover:bg-slate-800 sm:right-4 sm:top-4"
                            @click.stop="close()"
                        >
                            Zavřít (Esc)
                        </button>

                        <div class="relative z-[101] flex max-h-[90vh] max-w-[min(100vw-2rem,1200px)] flex-col items-center justify-center">
                            <img
                                x-bind:key="'home-lb-' + i"
                                :src="peekSrc()"
                                :alt="peekAlt()"
                                class="max-h-[min(78vh,900px)] w-auto max-w-full rounded-lg object-contain shadow-2xl"
                                loading="eager"
                                decoding="async"
                                referrerpolicy="no-referrer-when-downgrade"
                                @click.stop
                            >
                            <p
                                x-show="peekCaption() !== ''"
                                x-text="peekCaption()"
                                class="mt-4 max-w-2xl text-center text-sm leading-relaxed text-slate-200"
                            ></p>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </section>

    <section id="kontakt" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-8 text-slate-200">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Kontakt</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white">Arytmie Praha</h2>
                </div>

                <dl class="space-y-4 text-sm leading-6">
                    <div>
                        <dt class="font-semibold text-white">Web</dt>
                        <dd class="mt-1">
                            <a href="https://www.arytmie-praha.cz" class="text-orange-300 hover:text-orange-200" rel="noopener noreferrer">www.arytmie-praha.cz</a>
                            <span class="text-slate-400"> (tyto stránky)</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-white">Facebook</dt>
                        <dd class="mt-1">
                            <a href="https://facebook.com/arytmieprahasbor/" class="text-orange-300 hover:text-orange-200 break-all" target="_blank" rel="noopener noreferrer">facebook.com/arytmieprahasbor</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-white">Instagram</dt>
                        <dd class="mt-1">
                            <a href="https://instagram.com/arytmieprahasbor/" class="text-orange-300 hover:text-orange-200 break-all" target="_blank" rel="noopener noreferrer">instagram.com/arytmieprahasbor</a>
                        </dd>
                    </div>
                </dl>

                <div class="rounded-2xl border border-slate-600 bg-slate-900/40 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-orange-300">Umělecká vedoucí</h3>
                    <p class="mt-2 text-lg font-semibold text-white">Lenka Menclová</p>
                    <p class="mt-3 text-sm">
                        E-mail:
                        <a href="mailto:lenka.menclova@msmt.cz" class="text-orange-300 hover:text-orange-200">lenka.menclova@msmt.cz</a>
                    </p>
                    <p class="mt-2 text-sm">
                        Mobil:
                        <a href="tel:+420602546338" class="text-orange-300 hover:text-orange-200">602&nbsp;546&nbsp;338</a>,
                        <a href="tel:+420602219686" class="text-orange-300 hover:text-orange-200">602&nbsp;219&nbsp;686</a>
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-600 bg-slate-900/40 p-5">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-orange-300">Zkušebna</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-200">
                        MŠ Matěchova<br>
                        Halasova 1069<br>
                        140&nbsp;00 Praha 4
                    </p>
                    <p class="mt-3 text-sm">
                        <a href="https://www.google.cz/maps/@50.0418428,14.4389896,17z" class="font-semibold text-orange-300 hover:text-orange-200" target="_blank" rel="noopener noreferrer">Mapa</a>
                    </p>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-600 bg-white p-6 text-stone-900 shadow-sm sm:p-8">
                @if (session('contact_status'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('contact_status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        <p class="font-semibold">Formulář se nepodařilo odeslat.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-lg font-semibold text-stone-900">Napište nám</h3>
                <p class="mt-2 text-sm text-stone-600">
                    Zpráva se uloží do databáze webu. Nevolejte prosím členům sboru kvůli obchodním nabídkám.
                </p>

                <form method="post" action="{{ route('contact.store') }}" class="mt-6 grid gap-4">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Jméno a příjmení" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm outline-none transition focus:border-orange-400">
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="E-mail" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm outline-none transition focus:border-orange-400">
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Telefon (nepovinné)" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm outline-none transition focus:border-orange-400">
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Předmět (nepovinné)" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm outline-none transition focus:border-orange-400">
                    <textarea name="message" rows="5" required placeholder="Vaše zpráva" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm outline-none transition focus:border-orange-400">{{ old('message') }}</textarea>

                    <label class="flex items-start gap-3 text-sm text-stone-700">
                        <input type="checkbox" name="consented_to_processing" value="1" class="mt-1 rounded border-stone-300 text-orange-500 focus:ring-orange-400" @checked(old('consented_to_processing')) required>
                        <span>Souhlasím se zpracováním údajů za účelem odpovědi na tuto zprávu.</span>
                    </label>

                    @if (config('services.turnstile.enabled') && filled(config('services.turnstile.site_key')))
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    @endif

                    <button type="submit" class="rounded-full bg-orange-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Odeslat zprávu
                    </button>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.querySelectorAll('img[data-home-gallery-thumb]').forEach((img) => {
                img.addEventListener('error', () => {
                    const fb = img.dataset.fallbackSrc;
                    if (fb && img.dataset.fallbackDone !== '1') {
                        img.dataset.fallbackDone = '1';
                        img.src = fb;
                    }
                });
            });
        </script>
    @endpush
@endsection
