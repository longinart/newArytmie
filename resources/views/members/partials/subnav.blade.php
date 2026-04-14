@php
    $links = [
        ['route' => 'members.harmonogram', 'label' => 'Harmonogram'],
        ['route' => 'members.naslechy', 'label' => 'Náslechy'],
        ['route' => 'members.noty', 'label' => 'Noty'],
    ];
@endphp
<nav class="mb-10 mt-8 border-b border-slate-600/80 pb-6 sm:mt-10" aria-label="Členská sekce">
    <ul class="flex flex-wrap gap-2 sm:gap-3">
        @foreach ($links as $link)
            <li>
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'rounded-full px-4 py-2 text-sm font-semibold transition',
                        'bg-orange-500 text-white' => request()->routeIs($link['route']),
                        'border border-slate-500 text-slate-200 hover:border-orange-400 hover:text-white' => ! request()->routeIs($link['route']),
                    ])
                >
                    {{ $link['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <form method="post" action="{{ route('members.lock') }}" class="mt-4">
        @csrf
        <button
            type="submit"
            class="text-sm font-medium text-slate-400 underline-offset-2 transition hover:text-orange-300 hover:underline"
        >
            Zavřít celou členskou sekci
        </button>
    </form>
</nav>
