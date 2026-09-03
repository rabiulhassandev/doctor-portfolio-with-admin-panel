{{-- One service in the grid: icon, title, blurb. --}}

@props([
    'service',
    'delay' => 0,   // Stagger a row of cards by passing 0, 100, 200…

    // Where the card sits in the page outline. On the home page the grid
    // follows an <h2> section heading, so h3 is right. On /services the cards
    // are the section, and h3 would skip a level under the page <h1>.
    'level' => 'h3',
])

{{--
    No lift on hover.

    A card that jumps a pixel and grows a shadow every time the pointer crosses
    it makes a page of nine of them feel twitchy. What is left is quieter and
    does more work: the hairline darkens, the icon fills in, and the whole thing
    settles very slightly forward. Restraint is most of what separates this from
    a bootstrapped grid.
--}}
<article data-reveal="{{ $delay }}"
         class="group relative flex h-full flex-col rounded-xl border border-line bg-surface p-8
                transition-[border-color,box-shadow] duration-500 ease-[var(--ease-out-soft)]
                hover:border-line-strong hover:shadow-card">

    {{-- The icon name is a Heroicon chosen from a dropdown in the admin panel.
         A hairline ring rather than a filled chip: at rest it is drawn, on
         hover it is painted. --}}
    <span class="flex h-12 w-12 items-center justify-center rounded-lg border border-line bg-paper text-accent
                 transition-colors duration-500 group-hover:border-accent group-hover:bg-accent group-hover:text-white">
        <x-dynamic-component :component="$service->icon" class="h-6 w-6" />
    </span>

    <{{ $level }} class="mt-7 font-display text-2xl font-normal tracking-[-0.015em] text-ink">
        {{ $service->title }}
    </{{ $level }}>

    <p class="mt-3 leading-relaxed text-muted">
        {{ $service->short_description }}
    </p>

    @if ($service->description)
        {{--
            `mt-auto` pins the extra detail to the bottom of the card so the
            block starts on the same line in every card of a row. Letting it
            follow the short description instead left a row of cards with the
            small print at four different heights, which read as a mistake.
        --}}
        <p class="mt-auto border-t border-line pt-5 text-sm leading-relaxed text-muted">
            {{ $service->description }}
        </p>
    @else
        {{-- Nothing to pin, but the card still has to fill its grid cell. --}}
        <span class="flex-1" aria-hidden="true"></span>
    @endif
</article>
