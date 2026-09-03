@php
    use App\Support\Media;

    $photo = Media::url($doctor->photo);
    $qualifications = $doctor->qualifications ?? [];
@endphp

<x-layouts.app
    title="About"
    :description="$doctor->short_bio ?: 'Meet ' . $doctor->name . ', ' . $doctor->specialization . '.'">

    {{-- Structured data: this page is where Google learns who the doctor is. --}}
    @push('schema')
        <x-site.physician-schema />
    @endpush

    <x-ui.page-hero
        eyebrow="About"
        :title="'Meet ' . $doctor->name"
        :subtitle="$doctor->tagline" />

    {{-- ==================================================================
         Biography
         ================================================================== --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-14 lg:grid-cols-12 lg:gap-20">

                {{-- Portrait and facts. Sticky on a wide screen: the biography
                     runs long, and the doctor's face and address are worth
                     keeping in view the whole way down it. --}}
                <aside class="lg:col-span-4 lg:sticky lg:top-32 lg:self-start" data-reveal>
                    @if ($photo)
                        <img src="{{ $photo }}"
                             alt="Portrait of {{ $doctor->name }}"
                             class="aspect-[4/5] w-full rounded-sm object-cover shadow-lift"
                             width="480" height="600">
                    @endif

                    <dl class="mt-10 divide-y divide-line border-y border-line">
                        <div class="py-5">
                            <dt class="eyebrow">Specialisation</dt>
                            <dd class="mt-2 text-ink">{{ $doctor->specialization }}</dd>
                        </div>

                        @if ($doctor->registration())
                            <div class="py-5">
                                <dt class="eyebrow">{{ trim($doctor->registration_label ?? 'Registration', ' .') }}</dt>
                                <dd class="mt-2 tabular-nums text-ink">{{ $doctor->registration_number }}</dd>
                            </div>
                        @endif

                        @if ($doctor->chamber_name)
                            <div class="py-5">
                                <dt class="eyebrow">Chamber</dt>
                                <dd class="mt-2 text-ink">{{ $doctor->chamber_name }}</dd>
                            </div>
                        @endif

                        @if ($doctor->years_of_experience)
                            <div class="py-5">
                                <dt class="eyebrow">Experience</dt>
                                <dd class="mt-2 text-ink">{{ $doctor->years_of_experience }} years in practice</dd>
                            </div>
                        @endif

                        @if ($doctor->fullAddress())
                            <div class="py-5">
                                <dt class="eyebrow">Clinic</dt>
                                <dd class="mt-2 text-sm leading-relaxed text-ink">
                                    <address class="not-italic">{{ $doctor->fullAddress() }}</address>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <a href="{{ route('contact') }}#appointment"
                       class="mt-8 block rounded-full bg-brand px-6 py-3.5 text-center text-[15px] font-medium text-white transition-colors duration-300 hover:bg-brand-dark">
                        Book an appointment
                    </a>
                </aside>

                {{-- Long-form bio.
                     Capped at roughly 70 characters a line. The column has room
                     for half again as much, but a reader loses their place on a
                     line that long — the width of a page is a typographic
                     decision, not a layout one. --}}
                <div class="max-w-[38rem] lg:col-span-8" data-reveal="100">
                    @if ($doctor->bio)
                        <div class="prose-article">
                            {{-- The bio is plain text typed into the admin panel, so
                                 blank lines are turned into paragraphs here and the
                                 content is escaped — never rendered as raw HTML. --}}
                            @foreach (preg_split('/\R{2,}/', trim($doctor->bio)) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @else
                        <p class="text-lg leading-relaxed text-muted">{{ $doctor->short_bio }}</p>
                    @endif

                    {{-- Philosophy. A tinted panel with a thick left bar is how
                         a documentation site marks a callout; here the passage
                         is simply indented behind a hairline and set larger,
                         which is how a book marks one. --}}
                    @if ($doctor->philosophy)
                        <div class="mt-14 border-l border-accent pl-8 sm:pl-10">
                            <h2 class="text-3xl">My approach to care</h2>
                            <div class="mt-5 space-y-5 text-lg leading-[1.7] text-muted">
                                @foreach (preg_split('/\R{2,}/', trim($doctor->philosophy)) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         Qualifications
         ================================================================== --}}
    {{-- Set as a list rather than as cards. A qualification is one line of
         fact; wrapping each in a bordered panel with an icon inflates it into
         something it is not, and four inflated panels in a column is the most
         template-looking thing a page can do. Rules and a year in the margin
         is how a CV sets the same information. --}}
    @if ($qualifications !== [])
        <section class="border-y border-line bg-paper-shade py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="Credentials" title="Qualifications & training" :rule="false" />

                <ol class="mx-auto mt-16 max-w-3xl border-t border-line">
                    @foreach ($qualifications as $index => $qualification)
                        <li class="flex flex-wrap items-baseline gap-x-8 gap-y-1 border-b border-line py-7"
                            data-reveal="{{ $index * 70 }}">

                            <span class="w-16 shrink-0 font-display text-lg tabular-nums text-accent">
                                {{ $qualification['year'] ?? '' }}
                            </span>

                            <div class="flex-1">
                                <h3 class="font-display text-xl font-normal tracking-[-0.015em] text-ink">
                                    {{ $qualification['title'] ?? '' }}
                                </h3>
                                @if ($institution = $qualification['institution'] ?? null)
                                    <p class="mt-1 text-sm text-muted">{{ $institution }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- ==================================================================
         Services teaser
         ================================================================== --}}
    @if ($services->isNotEmpty())
        <section class="py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="Treatments" title="How I can help" :rule="false" />

                <div class="mt-16 grid gap-6 sm:grid-cols-2">
                    @foreach ($services as $index => $service)
                        <x-ui.service-card :service="$service" :delay="$index * 80" />
                    @endforeach
                </div>

                <div class="mt-14 text-center" data-reveal>
                    <a href="{{ route('services') }}"
                       class="inline-flex rounded-full border border-line-strong px-8 py-3.5 text-sm font-medium text-ink transition-colors duration-300 hover:border-ink">
                        See all services
                    </a>
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
