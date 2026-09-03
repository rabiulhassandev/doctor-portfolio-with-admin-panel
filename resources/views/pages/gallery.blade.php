@php
    use App\Support\Media;

    /*
     | Build a plain array for the lightbox so Alpine can page through the
     | photos without another request. Keeping this out of the markup means the
     | grid below and the lightbox can never disagree about the order.
     */
    $slides = $images->map(fn ($image) => [
        'src' => Media::url($image->image),
        'alt' => $image->altText(),
        'caption' => $image->caption,
    ])->values();
@endphp

<x-layouts.app
    title="Gallery"
    :description="'A look inside the clinic of ' . $doctor->name . '.'">

    <x-ui.page-hero
        eyebrow="Our space"
        title="Gallery"
        subtitle="A look around the clinic before your first visit." />

    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if ($images->isNotEmpty())
                {{--
                    Lightbox state:
                      open    : is the overlay showing?
                      current : index of the photo on screen
                      slides  : the array built above

                    Arrow keys move between photos and Escape closes, so the
                    gallery works without a mouse.
                --}}
                <div x-data="{
                        open: false,
                        current: 0,
                        opener: null,
                        slides: {{ Js::from($slides) }},
                        show(index, opener) {
                            // Remember the thumbnail so focus can go back to it
                            // on close — otherwise a keyboard user is dropped at
                            // the top of the page and has to tab all the way
                            // back to where they were.
                            this.opener = opener;
                            this.current = index;
                            this.open = true;
                            this.$nextTick(() => this.$refs.close.focus());
                        },
                        close() {
                            this.open = false;
                            this.opener?.focus();
                        },
                        next() { this.current = (this.current + 1) % this.slides.length; },
                        prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; },
                     }"
                     {{-- The page behind a full-screen overlay must not scroll
                          with it: on a phone the scroll gesture otherwise moves
                          the grid underneath the photo. --}}
                     x-effect="document.body.style.overflow = open ? 'hidden' : ''"
                     @keydown.escape.window="open && close()"
                     @keydown.arrow-right.window="open && next()"
                     @keydown.arrow-left.window="open && prev()">

                    {{--
                        Grid.

                        Six equal squares read as a contact sheet. Giving the
                        first photo four cells turns the same six pictures into
                        something that looks arranged, and it is the one the
                        doctor put first in the admin panel — so it is the one
                        worth showing large.

                        The fixed row heights from `sm` up are what make that
                        span possible; below `sm` the grid is two plain columns
                        and each tile is sized by its own 4:3 image.

                        Three columns rather than four: the feature tile takes
                        four cells, so the six seeded photos fill exactly three
                        rows with no hole left in the corner.
                    --}}
                    <ul class="grid grid-cols-2 gap-4 sm:auto-rows-[10rem] sm:grid-cols-3 lg:auto-rows-[13rem]">
                        @foreach ($images as $index => $image)
                            <li data-reveal="{{ ($index % 4) * 60 }}"
                                @class(['sm:col-span-2 sm:row-span-2' => $index === 0])>

                                <button type="button"
                                        @click="show({{ $index }}, $event.currentTarget)"
                                        {{-- The label carries the description, so
                                             the image itself is left silent rather
                                             than announced twice. --}}
                                        aria-label="Open larger: {{ $image->altText() }}"
                                        class="group relative block h-full w-full overflow-hidden rounded-sm bg-paper-shade focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2">

                                    <img src="{{ Media::url($image->image) }}"
                                         alt=""
                                         loading="lazy"
                                         class="aspect-4/3 w-full object-cover transition duration-500 group-hover:scale-105 sm:aspect-auto sm:h-full">

                                    {{-- Hover veil with a zoom icon, so it is obvious
                                         the photo opens larger. --}}
                                    <span class="absolute inset-0 flex items-center justify-center bg-ink-deep/0 transition-colors duration-500 group-hover:bg-ink-deep/30">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-full border border-white/50 text-white opacity-0 backdrop-blur-sm transition-opacity duration-500 group-hover:opacity-100">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                            </svg>
                                        </span>
                                    </span>

                                    @if ($image->caption)
                                        {{-- Sitting on the photo rather than under
                                             it keeps every tile the same height,
                                             which is what lets the rows line up. --}}
                                        <span class="pointer-events-none absolute inset-x-0 bottom-0 bg-linear-to-t from-ink-deep/80 via-ink-deep/30 to-transparent px-4 pb-3.5 pt-10 text-left text-sm font-medium text-white">
                                            {{ $image->caption }}
                                        </span>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Lightbox overlay --}}
                    <div x-show="open"
                         x-cloak
                         x-transition.opacity
                         class="fixed inset-0 z-[60] flex items-center justify-center bg-ink-deep/96 p-4 backdrop-blur-sm"
                         role="dialog"
                         aria-modal="true"
                         aria-label="Photo viewer">

                        {{-- Clicking the backdrop closes; clicking the photo does not. --}}
                        <button type="button"
                                @click="close()"
                                class="absolute inset-0 h-full w-full cursor-zoom-out"
                                tabindex="-1">
                            <span class="sr-only">Close the photo viewer</span>
                        </button>

                        <button type="button"
                                x-ref="close"
                                @click="close()"
                                class="absolute right-4 top-4 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                        @if ($images->count() > 1)
                            <button type="button"
                                    @click="prev()"
                                    class="absolute left-3 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 sm:left-8">
                                <span class="sr-only">Previous photo</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>

                            <button type="button"
                                    @click="next()"
                                    class="absolute right-3 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 sm:right-8">
                                <span class="sr-only">Next photo</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        @endif

                        <figure class="relative z-10 max-h-full w-full max-w-4xl text-center">
                            <img :src="slides[current].src"
                                 :alt="slides[current].alt"
                                 class="mx-auto max-h-[75vh] w-auto rounded-sm object-contain shadow-float">

                            <figcaption class="mt-4 text-white/80">
                                <span x-text="slides[current].caption"></span>
                                <span class="mt-1 block text-sm text-white/50">
                                    <span x-text="current + 1"></span> of <span x-text="slides.length"></span>
                                </span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            @else
                <p class="mx-auto max-w-lg text-center text-lg text-muted">
                    Photos of the clinic are on their way.
                </p>
            @endif
        </div>
    </section>
</x-layouts.app>
