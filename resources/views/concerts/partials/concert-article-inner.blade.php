<p class="text-sm text-orange-500">
    {{ $concert->starts_at?->format('d.m.Y H:i') }}
    @if($concert->ends_at)
        - {{ $concert->ends_at->format('d.m.Y H:i') }}
    @endif
</p>
<h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">{{ $concert->title }}</h1>
<p class="mt-3 text-stone-700">
    {{ $concert->venue_name }}, {{ $concert->city }}
    @if($concert->venue_address)
        - {{ $concert->venue_address }}
    @endif
</p>

@if ($showCover ?? false)
    <figure class="mt-6">
        <button
            type="button"
            class="group block w-full max-w-2xl cursor-zoom-in overflow-hidden rounded-2xl border border-orange-100 bg-stone-50 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400"
            @click="openAt(0)"
            aria-label="Zvětšit titulní fotku koncertu"
        >
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($concert->cover_image_path) }}"
                alt="{{ $concert->title }}"
                class="max-h-[28rem] w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                loading="eager"
                decoding="async"
                width="960"
                height="540"
            >
        </button>
    </figure>
@endif

@if($concert->program)
    <section class="mt-8">
        <h2 class="text-lg font-semibold text-stone-900">Program</h2>
        <div class="aktualita-body mt-2 max-w-none text-stone-700 [&_p+p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5 [&_a]:text-orange-600 [&_a]:underline">
            {!! \Illuminate\Support\Str::markdown($concert->program, ['html_input' => 'strip']) !!}
        </div>
    </section>
@endif

@if($concert->description)
    <section class="mt-8">
        <h2 class="text-lg font-semibold text-stone-900">Popis</h2>
        <div class="aktualita-body mt-2 max-w-none text-stone-700 [&_p+p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5 [&_a]:text-orange-600 [&_a]:underline">
            {!! \Illuminate\Support\Str::markdown($concert->description, ['html_input' => 'strip']) !!}
        </div>
    </section>
@endif

@if($concert->ticket_url)
    <a
        href="{{ $concert->ticket_url }}"
        target="_blank"
        rel="noopener noreferrer"
        class="mt-8 inline-flex rounded-full bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-600"
    >
        Vstupenky
    </a>
@endif
