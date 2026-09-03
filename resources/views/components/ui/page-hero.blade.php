{{--
    The banner at the top of every page except the home page.

    It carries the page's <h1> and enough top padding to clear the fixed navbar.

    The two blurred blobs that used to float in here have gone. Soft coloured
    circles behind a heading are the house style of a SaaS landing page; a
    consulting practice wants the opposite — a quiet field of paper, a hairline
    to close it off, and the name of the page set large enough to carry itself.
--}}

@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

<section class="relative border-b border-line bg-paper-shade pt-32 pb-14 sm:pt-40 sm:pb-20">
    {{-- A single wash from the top, which reads as light falling on the page
         rather than as a shape sitting behind it. Decorative. --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-2/3 bg-linear-to-b from-white/70 to-transparent" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
        <div class="max-w-3xl" data-reveal>
            @if ($eyebrow)
                <p class="eyebrow text-accent">{{ $eyebrow }}</p>
            @endif

            <h1 class="mt-5 text-[2.25rem] leading-[1.08] sm:text-[3.25rem]">
                {{ $title }}
            </h1>

            @if ($subtitle)
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted">
                    {{ $subtitle }}
                </p>
            @endif

            {{ $slot }}
        </div>
    </div>
</section>
