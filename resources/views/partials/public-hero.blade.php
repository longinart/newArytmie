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
    {{-- Spodní / horní přechod do barvy stránky — bez úpravy samotného souboru fotky. --}}
    <div class="relative w-full overflow-hidden bg-stone-200">
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
        <div
            class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-20 bg-gradient-to-b from-transparent via-slate-800/60 to-slate-700 sm:h-28 md:h-36"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute inset-x-0 top-0 z-10 h-14 bg-gradient-to-b from-slate-900/55 to-transparent sm:h-20"
            aria-hidden="true"
        ></div>
    </div>
@endif
