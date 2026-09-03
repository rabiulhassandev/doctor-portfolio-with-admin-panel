{{--
    Sticky site navigation with a mobile hamburger menu.

    All of the behaviour is Alpine:
      - `open`     : is the mobile drawer showing?
      - `scrolled` : has the visitor scrolled past the top of the page? Used to
                     fade in a solid background over the home-page hero.

    Once scrolled, the bar closes itself off with a hairline rather than a drop
    shadow. A shadow under a full-width bar makes it hover over the page like a
    toolbar; a rule makes it part of the page, which is what a masthead is.
--}}

@props([
    // The home page puts the hero image behind the navbar, so it starts clear.
    'transparent' => false,
])

@php
    $logo = config('site.logo');

    // Every nav item in one array — add a page here and both the desktop bar and
    // the mobile drawer pick it up.
    $links = array_filter([
        ['route' => 'home', 'label' => 'Home'],
        ['route' => 'about', 'label' => 'About'],
        ['route' => 'services', 'label' => 'Services'],
        config('site.features.gallery') ? ['route' => 'gallery', 'label' => 'Gallery'] : null,
        config('site.features.blog') ? ['route' => 'blog.index', 'label' => 'Blog'] : null,
        ['route' => 'contact', 'label' => 'Contact'],
    ]);

    $initials = Str::of($doctor->name)->replace('Dr.', '')->trim()->explode(' ')
        ->take(2)->map(fn ($w) => Str::substr($w, 0, 1))->implode('');
@endphp

<header
    x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 20"
    @scroll.window="scrolled = window.scrollY > 20"
    @keydown.escape.window="open = false"
    class="fixed inset-x-0 top-0 z-50 transition-colors duration-500 ease-[var(--ease-out-soft)]"
    :class="(scrolled || open || {{ $transparent ? 'false' : 'true' }})
        ? 'border-b border-line bg-paper/85 backdrop-blur-md'
        : 'border-b border-transparent bg-transparent'"
>
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-5 lg:px-8"
         aria-label="Main navigation">

        {{-- Wordmark --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3.5">
            @if ($logo)
                <img src="{{ asset($logo) }}"
                     alt="{{ $doctor->name }}"
                     class="h-10 w-auto">
            @else
                {{-- No logo uploaded: initials in a hairline circle. Set in the
                     display serif, so the monogram reads as a mark rather than
                     as a user avatar. --}}
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-brand/25 font-display text-base text-brand">
                    {{ $initials }}
                </span>
            @endif

            <span class="leading-tight">
                <span class="block font-display text-lg text-ink sm:text-xl">{{ $doctor->name }}</span>
                <span class="block text-[11px] uppercase tracking-[0.14em] text-muted">{{ $doctor->specialization }}</span>
            </span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-8 lg:flex">
            @foreach ($links as $link)
                @php $isCurrent = request()->routeIs($link['route']) || ($link['route'] === 'blog.index' && request()->routeIs('blog.*')); @endphp
                {{-- The underline grows from the left on hover and stays put on
                     the current page, so the indicator and the hover state are
                     the same object rather than two competing signals. --}}
                <a href="{{ route($link['route']) }}"
                   @if ($isCurrent) aria-current="page" @endif
                   class="group relative py-1 text-sm transition-colors duration-300 {{ $isCurrent ? 'text-ink' : 'text-muted hover:text-ink' }}">
                    {{ $link['label'] }}
                    <span @class([
                            'absolute -bottom-0.5 left-0 h-px w-full origin-left bg-accent transition-transform duration-500 ease-[var(--ease-out-soft)]',
                            'scale-x-100' => $isCurrent,
                            'scale-x-0 group-hover:scale-x-100' => ! $isCurrent,
                        ]) aria-hidden="true"></span>
                </a>
            @endforeach

            <a href="{{ route('contact') }}#appointment"
               class="rounded-full bg-brand px-6 py-3 text-sm font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
                Book an appointment
            </a>
        </div>

        {{-- Mobile hamburger --}}
        <button type="button"
                @click="open = ! open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-menu"
                class="-mr-2 inline-flex items-center justify-center rounded-lg p-2 text-ink transition-colors hover:bg-paper-shade lg:hidden">
            <span class="sr-only">Toggle navigation menu</span>
            {{-- Two icons, one shown at a time, so the state is obvious. --}}
            <svg x-show="! open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" d="M3.75 7.5h16.5M3.75 12h16.5M3.75 16.5h16.5" />
            </svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </nav>

    {{-- Mobile drawer --}}
    <div id="mobile-menu"
         x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.outside="open = false"
         class="border-t border-line bg-paper lg:hidden">
        <div class="px-6 py-5">
            <div class="divide-y divide-line">
                @foreach ($links as $link)
                    @php $isCurrent = request()->routeIs($link['route']) || ($link['route'] === 'blog.index' && request()->routeIs('blog.*')); @endphp
                    <a href="{{ route($link['route']) }}"
                       @click="open = false"
                       @if ($isCurrent) aria-current="page" @endif
                       class="flex items-center justify-between py-3.5 text-base transition-colors {{ $isCurrent ? 'text-ink' : 'text-muted' }}">
                        {{ $link['label'] }}
                        @if ($isCurrent)
                            <span class="h-1.5 w-1.5 rounded-full bg-accent" aria-hidden="true"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <a href="{{ route('contact') }}#appointment"
               @click="open = false"
               class="mt-6 block rounded-full bg-brand px-5 py-3.5 text-center text-base font-medium text-white">
                Book an appointment
            </a>

            @if ($doctor->telHref())
                <a href="{{ $doctor->telHref() }}"
                   class="mt-3 flex items-center justify-center gap-2 rounded-full border border-line px-5 py-3.5 text-center text-base text-ink">
                    <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                    {{ $doctor->phone }}
                </a>
            @endif
        </div>
    </div>
</header>
