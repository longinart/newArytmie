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
                Materiály k poslechu a přípravě. Obsah přidává administrátorka webu.
            </p>

            @include('members.partials.subnav')

            <div class="space-y-10">
                @forelse ($resources as $resource)
                    <article class="rounded-2xl border border-slate-600/80 bg-slate-900/40 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-white">{{ $resource->title }}</h2>
                        @if (filled($resource->body_markdown))
                            <div
                                class="aktualita-body mt-4 max-w-none text-sm leading-6 text-slate-300 [&_p+p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:pl-5 [&_a]:text-orange-400 [&_a]:underline"
                            >
                                {!! \Illuminate\Support\Str::markdown($resource->body_markdown, ['html_input' => 'strip']) !!}
                            </div>
                        @endif
                        @if ($resource->files->isNotEmpty())
                            <ul class="mt-4 space-y-2 border-t border-slate-600/60 pt-4 text-sm">
                                @foreach ($resource->files as $file)
                                    <li>
                                        <a
                                            href="{{ route('members.file.download', $file) }}"
                                            class="font-medium text-orange-400 underline-offset-2 hover:text-orange-300 hover:underline"
                                        >
                                            {{ $file->original_name }}
                                        </a>
                                        @if ($file->size_bytes)
                                            <span class="text-slate-500">({{ number_format($file->size_bytes / 1024, 0, ',', ' ') }} kB)</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @empty
                    <p class="rounded-2xl border border-dashed border-slate-600 bg-slate-900/30 p-8 text-center text-sm text-slate-400">
                        Zatím zde nejsou žádné položky — doplní je administrátorka v administraci webu.
                    </p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
