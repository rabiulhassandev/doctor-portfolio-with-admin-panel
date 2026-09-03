{{-- One article in the blog grid: cover image, date, title, excerpt. --}}

@props([
    'post',
    'delay' => 0,

    // Where the card sits in the page outline. On the home page the grid
    // follows an <h2> section heading, so h3 is right. On /blog the cards are
    // the section, and h3 would skip a level under the page <h1>.
    'level' => 'h3',
])

@php
    use App\Support\Media;

    $cover = Media::url($post->cover_image);
@endphp

{{--
    Borderless.

    Outlining an article card boxes the photograph in, and a page of six boxes
    reads as a grid of components. Without the border the covers sit on the
    paper and the page reads as a contents page — which is what it is. The
    hairline under the meta line does the separating instead.
--}}
<article data-reveal="{{ $delay }}" class="group flex h-full flex-col">

    <a href="{{ route('blog.show', $post->slug) }}"
       class="block overflow-hidden rounded-lg bg-paper-shade"
       tabindex="-1"
       aria-hidden="true">
        @if ($cover)
            <img src="{{ $cover }}"
                 alt=""
                 loading="lazy"
                 class="aspect-[3/2] w-full object-cover transition-transform duration-700 ease-[var(--ease-out-soft)] group-hover:scale-[1.03]">
        @else
            {{-- No cover uploaded: a paper-toned panel keeps the grid even. --}}
            <div class="flex aspect-[3/2] w-full items-center justify-center border border-line">
                <svg class="h-10 w-10 text-line-strong" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M6.75 12h9m-9 3h5.25M6.75 9h1.5" />
                </svg>
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col pt-6">
        <div class="flex items-center gap-2.5 text-xs text-muted">
            <time datetime="{{ $post->published_at?->toDateString() }}" class="font-medium tracking-wide">
                {{ $post->published_at?->format('j M Y') }}
            </time>
            <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>
            <span>{{ $post->readingMinutes() }} min read</span>
        </div>

        <{{ $level }} class="mt-3 font-display text-2xl font-normal leading-[1.2] tracking-[-0.015em] text-ink">
            <a href="{{ route('blog.show', $post->slug) }}"
               class="transition-colors duration-300 hover:text-brand">
                {{ $post->title }}
            </a>
        </{{ $level }}>

        <p class="mt-3 flex-1 leading-relaxed text-muted">
            {{ $post->excerpt(130) }}
        </p>

        <span class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-brand" aria-hidden="true">
            Read the article
            <svg class="h-3.5 w-3.5 transition-transform duration-500 ease-[var(--ease-out-soft)] group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </span>
    </div>
</article>
