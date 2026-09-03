@php
    use App\Support\Media;

    $cover = Media::url($post->cover_image);
@endphp

<x-layouts.app
    :title="$post->meta_title ?: $post->title"
    :description="$post->meta_description ?: $post->excerpt(160)"
    :image="$cover">

    {{-- Article structured data, so the post can appear as a rich result. --}}
    @push('schema')
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->excerpt(160),
                'image' => Media::absoluteUrl($post->cover_image),
                'datePublished' => $post->published_at?->toAtomString(),
                'dateModified' => $post->updated_at?->toAtomString(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $doctor->name,
                    'jobTitle' => $doctor->specialization,
                ],
                'mainEntityOfPage' => route('blog.show', $post->slug),
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    {{--
        The article, wrapped in the reading-progress bar's Alpine scope.

        A long health article is intimidating without one, and the bar is the
        cheapest way to say "there is a minute of this left, keep going". It
        measures the <article> rather than the whole document, so the related
        posts and the footer underneath do not count against a piece the reader
        has already finished.
    --}}
    <div x-data="{
            progress: 0,
            track() {
                const article = this.$refs.article;
                const start = article.offsetTop;
                // How far there is to scroll before the end of the article
                // reaches the bottom of the window.
                const distance = article.offsetHeight - window.innerHeight;

                this.progress = distance <= 0
                    ? 100
                    : Math.min(100, Math.max(0, ((window.scrollY - start) / distance) * 100));
            },
         }"
         x-init="track()"
         @scroll.window.passive="track()"
         @resize.window.passive="track()">

        {{-- aria-hidden: the byline already says "6 min read", and a
             continuously changing percentage is noise in a screen reader. --}}
        <div class="fixed inset-x-0 top-0 z-[60] h-1" aria-hidden="true">
            <div class="h-full bg-accent transition-[width] duration-150 ease-out"
                 :style="`width: ${progress}%`"></div>
        </div>

    <article x-ref="article">
        {{-- ==============================================================
             Header
             ============================================================== --}}
        {{-- Centred, on plain paper. A tinted band behind a headline is a
             blog-theme convention; a title standing on the page with a rule
             under the byline is how a periodical opens a piece. --}}
        <header class="pt-36 pb-12 sm:pt-44 sm:pb-14">
            <div class="mx-auto max-w-3xl px-6 text-center lg:px-8">
                <nav aria-label="Breadcrumb">
                    <ol class="flex items-center justify-center gap-2 text-xs uppercase tracking-[0.14em] text-muted">
                        <li><a href="{{ route('home') }}" class="transition-colors hover:text-ink">Home</a></li>
                        <li aria-hidden="true" class="text-line-strong">/</li>
                        <li><a href="{{ route('blog.index') }}" class="transition-colors hover:text-ink">Blog</a></li>
                    </ol>
                </nav>

                <h1 class="mt-7 text-[2rem] leading-[1.12] sm:text-[2.625rem] lg:text-[3.125rem]">
                    {{ $post->title }}
                </h1>

                <div class="mx-auto mt-8 flex max-w-md flex-wrap items-center justify-center gap-x-3 gap-y-2 border-t border-line pt-6 text-sm text-muted">
                    <span class="text-ink">{{ $doctor->name }}</span>
                    <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>
                    <time datetime="{{ $post->published_at?->toDateString() }}">
                        {{ $post->published_at?->format('j F Y') }}
                    </time>
                    <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>
                    <span>{{ $post->readingMinutes() }} min read</span>
                </div>
            </div>
        </header>

        {{-- ==============================================================
             Cover image
             ============================================================== --}}
        @if ($cover)
            <div class="mx-auto max-w-5xl px-6 lg:px-8">
                <img src="{{ $cover }}"
                     alt="{{ $post->title }}"
                     class="aspect-[16/9] w-full rounded-sm object-cover"
                     width="1200" height="630">
            </div>
        @endif

        {{-- ==============================================================
             Body
             ============================================================== --}}
        <div class="py-16 sm:py-24">
            <div class="mx-auto max-w-2xl px-6 lg:px-8">
                {{--
                    The content comes from Filament's rich text editor, which
                    produces trusted HTML written by the site owner — so it is
                    rendered unescaped on purpose. Never pipe visitor-submitted
                    content through here.
                --}}
                <div class="prose-article">
                    {!! $post->content !!}
                </div>

                {{-- Share --}}
                <div class="mt-16 flex flex-wrap items-center gap-3 border-t border-line pt-8">
                    <span class="eyebrow mr-2">Share</span>

                    <a href="https://wa.me/?text={{ rawurlencode($post->title . ' ' . route('blog.show', $post->slug)) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="rounded-full border border-line px-4 py-2 text-sm text-muted transition-colors duration-300 hover:border-line-strong hover:text-ink">
                        WhatsApp
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="rounded-full border border-line px-4 py-2 text-sm text-muted transition-colors duration-300 hover:border-line-strong hover:text-ink">
                        Facebook
                    </a>

                    <a href="mailto:?subject={{ rawurlencode($post->title) }}&body={{ rawurlencode(route('blog.show', $post->slug)) }}"
                       class="rounded-full border border-line px-4 py-2 text-sm text-muted transition-colors duration-300 hover:border-line-strong hover:text-ink">
                        Email
                    </a>
                </div>
            </div>
        </div>
    </article>
    </div>{{-- /reading progress scope --}}

    {{-- ==================================================================
         Related reading
         ================================================================== --}}
    @if ($related->isNotEmpty())
        <section class="border-y border-line bg-paper-shade py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="Keep reading" title="More articles" :rule="false" />

                <div class="mx-auto mt-16 grid max-w-4xl gap-10 sm:grid-cols-2">
                    @foreach ($related as $index => $relatedPost)
                        <x-ui.post-card :post="$relatedPost" :delay="$index * 80" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Call to action --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-3xl px-6 text-center lg:px-8" data-reveal>
            <h2 class="text-[1.875rem] leading-[1.12] sm:text-[2.25rem]">
                Have a question about your own health?
            </h2>
            <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-muted">
                Articles are general guidance. For advice about your situation, book a consultation.
            </p>
            <a href="{{ route('contact') }}#appointment"
               class="mt-9 inline-flex rounded-full bg-brand px-8 py-4 text-[15px] font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
                Request an appointment
            </a>
        </div>
    </section>
</x-layouts.app>
