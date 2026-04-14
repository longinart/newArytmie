<header class="border-b border-slate-700 bg-slate-900/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-left transition hover:opacity-90">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Arytmie Praha</p>
            <p class="text-sm text-slate-300">Ženský komorní sbor</p>
        </a>

        <nav class="hidden gap-6 text-sm font-medium text-slate-200 md:flex">
            <a href="{{ route('about') }}" class="transition hover:text-orange-500">O nás</a>
            <a href="{{ route('home') }}#aktuality" class="transition hover:text-orange-500">Aktuality</a>
            <a href="{{ route('home') }}#koncerty" class="transition hover:text-orange-500">Koncerty</a>
            <a href="{{ route('gallery.index') }}" class="transition hover:text-orange-500">Galerie</a>
            @if (config('members.enabled'))
                <a href="{{ route('members.index') }}" class="transition hover:text-orange-500">Pro členky sboru</a>
            @endif
            <a href="{{ route('home') }}#kontakt" class="transition hover:text-orange-500">Kontakt</a>
        </nav>
    </div>
</header>
