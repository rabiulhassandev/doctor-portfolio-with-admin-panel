<x-layouts.app
    title="Health articles"
    :description="'Health guidance and clinic news from ' . $doctor->name . '.'">

    <x-ui.page-hero
        eyebrow="Blog"
        title="Health articles"
        subtitle="Plain-English guidance on staying well, written for patients." />

    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if ($posts->isNotEmpty())
                <div class="grid gap-x-10 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $index => $post)
                        <x-ui.post-card :post="$post" :delay="($index % 3) * 80" level="h2" />
                    @endforeach
                </div>

                {{-- Laravel's Tailwind pagination markup; hidden automatically
                     when everything fits on one page. --}}
                <div class="mt-20 border-t border-line pt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <p class="mx-auto max-w-lg text-center text-lg text-muted">
                    The first articles are being written. Please check back soon.
                </p>
            @endif
        </div>
    </section>
</x-layouts.app>
