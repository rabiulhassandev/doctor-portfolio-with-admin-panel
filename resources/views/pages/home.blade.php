@php
    use App\Support\Media;

    $photo = Media::url($doctor->photo);

    /*
     | One cycle of an ECG trace, drawn relative so it can be repeated end to
     | end: flat baseline, the small P bump, the QRS spike, then the broader T
     | wave. It is 240 units wide and returns to exactly the height it started
     | at, which is what lets it tile without a visible seam.
     |
     | This is the hero's background texture. A cardiologist's page is the one
     | place the motif is not decoration for its own sake — but it only works
     | if it stays almost invisible, so it is drawn as a hairline at very low
     | opacity rather than as a graphic anybody is meant to notice.
     */
    $ecgCycle = 'h40 c8 -10 16 -10 24 0 h18 l8 6 l8 -46 l8 58 l8 -18 h22 c14 -20 30 -20 44 0 h60';
    $ecgPath = 'M0 60 '.str_repeat($ecgCycle.' ', 8);
@endphp

<x-layouts.app :transparent-nav="true">

    {{-- ==================================================================
         Hero

         Asymmetric on purpose: seven columns of type against five of
         photograph, rather than the even 50/50 split a template reaches for.
         The imbalance is what makes the page look laid out rather than
         generated.
         ================================================================== --}}
    <section class="relative flex items-center overflow-hidden border-b border-line bg-paper-shade pt-32 pb-24 surface-grain sm:pt-40 sm:pb-28 lg:min-h-[92vh] lg:pb-32">

        {{--
            The ground the hero stands on, built in four layers. Each one is
            weak enough to be invisible on its own; together they are the
            difference between a section with a background colour and a section
            with a background. All decorative, so all hidden from assistive
            tech and all inert to the pointer.
        --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">

            {{-- 1. Light from above, so the section is not one flat field. --}}
            <div class="absolute inset-x-0 top-0 h-2/3 bg-linear-to-b from-white/80 to-transparent"></div>

            {{-- 2. A tinted panel anchoring the right side, bleeding off the
                 edge. This is what the portrait sits against — without it the
                 photograph floats in the middle of nothing. --}}
            <div class="absolute -right-24 top-0 hidden h-full w-[46%] rounded-l-[6rem] bg-linear-to-br from-brand/16 via-accent/10 to-transparent lg:block"></div>

            {{-- 3. Two soft colour washes, well out of focus. --}}
            <div class="absolute -top-32 right-1/4 h-[32rem] w-[32rem] rounded-full bg-accent/10 blur-[120px]"></div>
            <div class="absolute -bottom-40 -left-24 h-[28rem] w-[28rem] rounded-full bg-brand/10 blur-[120px]"></div>

            {{-- 4. The ECG trace. Kept down in the empty band at the foot of
                 the section: run any higher and it crosses the stat figures,
                 and a line through a number reads as a mistake rather than as
                 texture. Masked at both ends so it fades in and out rather
                 than stopping dead. --}}
            <svg class="absolute inset-x-0 bottom-6 w-full text-brand/[0.16] sm:bottom-8"
                 viewBox="0 0 1920 120"
                 preserveAspectRatio="xMidYMid slice"
                 style="mask-image: linear-gradient(to right, transparent, black 18%, black 82%, transparent);
                        -webkit-mask-image: linear-gradient(to right, transparent, black 18%, black 82%, transparent);">
                <path d="{{ $ecgPath }}" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
            </svg>
        </div>

        <div class="relative mx-auto grid w-full max-w-7xl items-center gap-16 px-6 lg:grid-cols-12 lg:gap-12 lg:px-8">

            {{-- Copy --}}
            <div class="lg:col-span-7 lg:pr-12" data-reveal>
                <div class="flex flex-wrap items-center gap-3">
                    <p class="eyebrow text-accent">{{ $doctor->specialization }}</p>

                    <span class="hidden h-3 w-px bg-line-strong sm:block" aria-hidden="true"></span>

                    {{-- Whether the clinic is open right now. Nothing else on
                         the page answers that without scrolling to the footer. --}}
                    <x-ui.open-status />
                </div>

                {{-- Sized to survive a long name. "Dr. Nafis Ahmed Chowdhury"
                     is three words more than "Dr. Amelia Hart", and a heading
                     tuned to the short one breaks into four lines on the long
                     one. --}}
                <h1 class="mt-7 text-[2.5rem] leading-[1.05] sm:text-[3.25rem] lg:text-[3.875rem]">
                    {{ $doctor->name }}
                </h1>

                @if ($doctor->chamber_name || $doctor->registration())
                    <p class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                        @if ($doctor->chamber_name)
                            <span class="text-ink">{{ $doctor->chamber_name }}</span>
                        @endif
                        @if ($doctor->chamber_name && $doctor->registration())
                            <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>
                        @endif
                        @if ($doctor->registration())
                            {{-- Patients here genuinely check this. Putting it
                                 above the fold is not decoration. --}}
                            <span class="tabular-nums">{{ $doctor->registration() }}</span>
                        @endif
                    </p>
                @endif

                @if ($doctor->tagline)
                    <p class="mt-7 max-w-lg text-lg leading-[1.65] text-muted">
                        {{ $doctor->tagline }}
                    </p>
                @endif

                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a href="{{ route('contact') }}#appointment"
                       class="rounded-full bg-brand px-8 py-4 text-[15px] font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
                        Book an appointment
                    </a>

                    @if ($doctor->telHref())
                        {{-- Two lines, because in Bangladesh the phone *is* the
                             booking system — "serial" is the word every patient
                             uses, and it belongs on the button rather than in a
                             caption underneath it. --}}
                        <a href="{{ $doctor->telHref() }}"
                           class="inline-flex items-center gap-3 rounded-full border border-line bg-surface py-2.5 pl-4 pr-7 text-left transition-colors duration-300 hover:border-line-strong">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-soft text-accent" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </span>
                            <span class="leading-tight">
                                <span class="block text-[10px] uppercase tracking-[0.14em] text-muted">Call for serial</span>
                                <span class="block text-[15px] font-medium tabular-nums text-ink">{{ $doctor->phone }}</span>
                            </span>
                        </a>
                    @endif
                </div>

                {{-- Trust markers. Divided by hairlines rather than spaced apart,
                     which is how a masthead sets its numbers. --}}
                <dl class="mt-14 grid max-w-lg grid-cols-3 divide-x divide-line border-t border-line pt-8">
                    @if ($doctor->years_of_experience)
                        <div class="pr-6">
                            <dt class="eyebrow">Experience</dt>
                            <dd class="mt-2 font-display text-3xl text-ink">{{ $doctor->years_of_experience }}+ <span class="text-xl text-muted">yrs</span></dd>
                        </div>
                    @endif

                    @if ($services->isNotEmpty())
                        <div class="px-6">
                            <dt class="eyebrow">Services</dt>
                            <dd class="mt-2 font-display text-3xl text-ink">{{ $serviceCount }}</dd>
                        </div>
                    @endif

                    @if (($doctor->qualifications ?? []) !== [])
                        <div class="pl-6">
                            <dt class="eyebrow">Qualifications</dt>
                            <dd class="mt-2 font-display text-3xl text-ink">{{ count($doctor->qualifications) }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Portrait --}}
            <div class="relative lg:col-span-5" data-reveal="150">
                @if ($photo)
                    {{-- The bottom padding leaves room for the credential card
                         to hang off the corner of the photo without being
                         clipped by the section. --}}
                    <div class="relative mx-auto max-w-sm pb-10 lg:ml-auto lg:mr-0 lg:max-w-none">
                        {{-- Wraps the photo alone, so the frame behind it is
                             measured against the photo and not against the
                             padding that makes room for the card below. --}}
                        <div class="relative">
                            {{-- A hairline frame offset behind the photograph.
                                 The flat teal panel that used to sit here read
                                 as a coloured rectangle; an outline reads as a
                                 mount. Decorative. --}}
                            <div class="absolute -bottom-5 -right-5 h-full w-full rounded-sm border border-line-strong" aria-hidden="true"></div>

                            {{-- The largest thing above the fold, so tell the
                                 browser to fetch it ahead of the rest. Squared
                                 corners: rounding a portrait to 24px is a
                                 product-page move, and a plate on a wall is
                                 what this wants to be. --}}
                            <img src="{{ $photo }}"
                                 alt="Portrait of {{ $doctor->name }}, {{ $doctor->specialization }}"
                                 class="relative aspect-[4/5] w-full rounded-sm object-cover shadow-float"
                                 width="640" height="800"
                                 fetchpriority="high">
                        </div>

                        @if ($doctor->years_of_experience)
                            {{--
                                A small card overlapping the bottom-left corner
                                of the photo. It repeats the experience figure
                                from the stats row on purpose: this is the half
                                of the hero a visitor's eye lands on first, and
                                on a phone the two sit far enough apart not to
                                read as a duplicate.
                            --}}
                            <div class="absolute bottom-0 left-0 flex items-center gap-4 rounded-sm border border-line bg-paper/95 px-6 py-5 shadow-lift backdrop-blur sm:-left-6">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-accent/30 text-accent" aria-hidden="true">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.746 3.746 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                </span>

                                <span class="leading-tight">
                                    <span class="block font-display text-2xl text-ink">{{ $doctor->years_of_experience }}+ years</span>
                                    <span class="mt-1 block text-xs uppercase tracking-[0.12em] text-muted">of clinical practice</span>
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ==================================================================
         About summary
         ================================================================== --}}
    @if ($doctor->short_bio || $doctor->bio)
        <section class="py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-20">
                    <div class="lg:col-span-4">
                        <x-ui.section-heading eyebrow="About" title="Care built on listening" align="start" class="max-w-none" />

                        {{-- A second frame of the same doctor. The column was
                             a heading and then two-thirds of nothing, which is
                             most of why the page read as empty rather than as
                             spacious. --}}
                        <img src="{{ Media::url('doctor/consulting.jpg') }}"
                             alt=""
                             loading="lazy"
                             class="mt-10 hidden aspect-[4/5] w-full rounded-sm object-cover shadow-card lg:block">
                    </div>

                    <div class="lg:col-span-8" data-reveal="100">
                        {{-- The opening paragraph set large, the way a magazine
                             sets a standfirst. --}}
                        <p class="text-xl leading-[1.65] text-ink">
                            {{ $doctor->short_bio ?: Str::limit(strip_tags($doctor->bio), 320) }}
                        </p>

                        @if (($doctor->qualifications ?? []) !== [])
                            <ul class="mt-10 grid gap-x-10 gap-y-6 border-t border-line pt-8 sm:grid-cols-2">
                                @foreach (array_slice($doctor->qualifications, 0, 4) as $qualification)
                                    <li class="flex gap-3.5">
                                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span class="text-sm leading-relaxed">
                                            <span class="block font-medium text-ink">{{ $qualification['title'] ?? '' }}</span>
                                            <span class="block text-muted">
                                                {{ collect([$qualification['institution'] ?? null, $qualification['year'] ?? null])->filter()->implode(', ') }}
                                            </span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <a href="{{ route('about') }}"
                           class="group mt-10 inline-flex items-center gap-2.5 text-sm font-medium text-brand">
                            Read the full story
                            <svg class="h-3.5 w-3.5 transition-transform duration-500 ease-[var(--ease-out-soft)] group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ==================================================================
         Practice at a glance

         A full-width photograph with the numbers over it. The page up to here
         is paper and hairlines, and it needs somewhere to land — a dark band
         with a picture behind it gives the eye a floor and stops the whole
         thing reading as one continuous sheet of white.

         Every figure comes from data already on the profile, so nothing here
         claims anything the doctor has not entered themselves.
         ================================================================== --}}
    <section class="relative overflow-hidden bg-ink-deep">
        <img src="{{ Media::url('site/dhaka.jpg') }}"
             alt=""
             loading="lazy"
             class="absolute inset-0 h-full w-full object-cover opacity-40">
        {{-- Two washes: one to sink the photograph far enough for white text to
             sit on it comfortably, one of accent from the corner. The gradient
             runs left-to-right so the type side stays almost solid while the
             skyline is still legible on the right. --}}
        <div class="absolute inset-0 bg-linear-to-r from-ink-deep via-ink-deep/92 to-ink-deep/55" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 right-0 h-96 w-96 rounded-full bg-accent/20 blur-[110px]" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 sm:py-28 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-5" data-reveal>
                    <p class="eyebrow text-accent">Practice at a glance</p>
                    <h2 class="mt-5 text-[1.875rem] leading-[1.12] text-white sm:text-[2.25rem]">
                        Cardiology in Dhaka, without the guesswork
                    </h2>
                    <p class="mt-5 max-w-md leading-relaxed text-white/60">
                        Every patient leaves with their report explained, their medicines written out, and a clear
                        answer to what happens next.
                    </p>
                </div>

                <dl class="grid grid-cols-2 gap-x-8 gap-y-10 lg:col-span-7 lg:grid-cols-4" data-reveal="100">
                    @if ($doctor->years_of_experience)
                        <div class="border-t border-white/15 pt-5">
                            <dt class="text-[11px] uppercase tracking-[0.16em] text-white/45">Experience</dt>
                            <dd class="mt-3 font-display text-4xl text-white">{{ $doctor->years_of_experience }}+</dd>
                            <dd class="mt-1 text-sm text-white/50">years in practice</dd>
                        </div>
                    @endif

                    @if (($doctor->qualifications ?? []) !== [])
                        <div class="border-t border-white/15 pt-5">
                            <dt class="text-[11px] uppercase tracking-[0.16em] text-white/45">Training</dt>
                            <dd class="mt-3 font-display text-4xl text-white">{{ count($doctor->qualifications) }}</dd>
                            <dd class="mt-1 text-sm text-white/50">degrees &amp; fellowships</dd>
                        </div>
                    @endif

                    @if ($services->isNotEmpty())
                        <div class="border-t border-white/15 pt-5">
                            <dt class="text-[11px] uppercase tracking-[0.16em] text-white/45">In chamber</dt>
                            <dd class="mt-3 font-display text-4xl text-white">{{ $serviceCount }}</dd>
                            <dd class="mt-1 text-sm text-white/50">services offered</dd>
                        </div>
                    @endif

                    @if ($doctor->registration())
                        <div class="border-t border-white/15 pt-5">
                            <dt class="text-[11px] uppercase tracking-[0.16em] text-white/45">Registered</dt>
                            <dd class="mt-3 font-display text-2xl tabular-nums text-white">{{ $doctor->registration_number }}</dd>
                            <dd class="mt-1 text-sm text-white/50">{{ trim($doctor->registration_label ?? '', ' .') ?: 'Registration' }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         Services
         ================================================================== --}}
    @if ($services->isNotEmpty())
        <section class="border-b border-line bg-paper-shade py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="What we offer" title="Services" :rule="false">
                    Straightforward, evidence-based care for every stage of your treatment.
                </x-ui.section-heading>

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $index => $service)
                        <x-ui.service-card :service="$service" :delay="$index * 80" />
                    @endforeach
                </div>

                <div class="mt-14 text-center" data-reveal>
                    <a href="{{ route('services') }}"
                       class="inline-flex rounded-full border border-line-strong bg-surface px-8 py-3.5 text-sm font-medium text-ink transition-colors duration-300 hover:border-ink">
                        See all services
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ==================================================================
         How a visit works

         Three steps, numbered in the serif. This is the section that most
         reduces the phone calls a chamber gets asking what to bring — and for a
         first-time patient it is the difference between booking and hesitating.

         The copy is generic to any chamber, so it lives here rather than in the
         database. A buyer who wants to change it edits this block.
         ================================================================== --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-14 lg:grid-cols-12 lg:gap-20">
                <div class="lg:col-span-4">
                    <x-ui.section-heading eyebrow="Before you come" title="How a visit works" align="start" class="max-w-none" />
                </div>

                <ol class="lg:col-span-8">
                    @foreach ([
                        [
                            'title' => 'Take a serial',
                            'body' => 'Call the chamber during opening hours, or send the request form on this site and '
                                .'someone will call you back to confirm a time. Walk-in patients are seen where the '
                                .'evening allows, after those with a serial.',
                        ],
                        [
                            'title' => 'Bring your medicines and reports',
                            'body' => 'Bring the actual strips of everything you take, not a list, along with any previous '
                                .'ECG, echo or blood reports — the printed copies rather than photographs of them. Old '
                                .'reports are often the most useful thing in the room.',
                        ],
                        [
                            'title' => 'Leave knowing the plan',
                            'body' => 'If an ECG or echo is needed it is usually done the same evening and explained to '
                                .'you before you go. You will leave with your medicines written out clearly and a date '
                                .'for the next review.',
                        ],
                    ] as $index => $step)
                        <li class="flex gap-6 border-t border-line py-8 first:border-t-0 first:pt-0 sm:gap-10"
                            data-reveal="{{ $index * 90 }}">
                            <span class="w-10 shrink-0 font-display text-3xl leading-none text-accent" aria-hidden="true">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div>
                                <h3 class="font-display text-2xl font-normal tracking-[-0.015em] text-ink">{{ $step['title'] }}</h3>
                                <p class="mt-3 max-w-xl leading-relaxed text-muted">{{ $step['body'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         Testimonials
         ================================================================== --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-y border-line bg-paper-shade py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="Patient stories" title="What patients say" :rule="false" />

                {{--
                    Slider state lives in Alpine:
                      active  : index of the visible quote
                      total   : how many there are
                    Arrow keys work as well as the buttons.
                --}}
                <div x-data="{ active: 0, total: {{ $testimonials->count() }} }"
                     @keydown.left="active = (active - 1 + total) % total"
                     @keydown.right="active = (active + 1) % total"
                     class="relative mx-auto mt-16 max-w-3xl"
                     data-reveal="100">

                    {{-- No card. A quotation does not need a box around it —
                         the serif, the width and the whitespace already say
                         "this is someone speaking". --}}
                    <div role="region"
                         aria-roledescription="carousel"
                         aria-label="Patient testimonials"
                         tabindex="0"
                         class="text-center">

                        @foreach ($testimonials as $index => $testimonial)
                            <div x-show="active === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-700"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 @if ($index > 0) x-cloak @endif
                                 role="group"
                                 aria-roledescription="slide"
                                 aria-label="{{ $index + 1 }} of {{ $testimonials->count() }}">

                                <x-ui.star-rating :rating="$testimonial->rating" class="justify-center" />

                                <figure>
                                    <blockquote class="mt-8">
                                        {{-- Hanging the opening quote mark into
                                             the margin is a typesetting detail
                                             that costs one pseudo-element and
                                             makes the whole block look set
                                             rather than styled. --}}
                                        <p class="font-display text-[1.625rem] leading-[1.4] text-ink sm:text-[1.9375rem]">
                                            <span class="text-line-strong" aria-hidden="true">&ldquo;</span>{{ $testimonial->message }}<span class="text-line-strong" aria-hidden="true">&rdquo;</span>
                                        </p>
                                    </blockquote>

                                    <figcaption class="mt-10 flex items-center justify-center gap-3.5">
                                        @if ($photoUrl = Media::url($testimonial->photo))
                                            <img src="{{ $photoUrl }}"
                                                 alt=""
                                                 loading="lazy"
                                                 class="h-11 w-11 rounded-full object-cover">
                                        @else
                                            <span class="flex h-11 w-11 items-center justify-center rounded-full border border-line font-display text-sm text-muted" aria-hidden="true">
                                                {{ $testimonial->initials() }}
                                            </span>
                                        @endif

                                        <span class="text-left">
                                            <span class="block text-sm font-medium text-ink">{{ $testimonial->patient_name }}</span>
                                            @if ($testimonial->patient_title)
                                                <span class="block text-sm text-muted">{{ $testimonial->patient_title }}</span>
                                            @endif
                                        </span>
                                    </figcaption>
                                </figure>
                            </div>
                        @endforeach
                    </div>

                    {{-- Dots --}}
                    @if ($testimonials->count() > 1)
                        <div class="mt-12 flex items-center justify-center gap-4">
                            <button type="button"
                                    @click="active = (active - 1 + total) % total"
                                    class="flex h-9 w-9 items-center justify-center rounded-full border border-line text-muted transition-colors duration-300 hover:border-line-strong hover:text-ink">
                                <span class="sr-only">Previous testimonial</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>

                            {{-- The dot is 6px tall by design, but a 6px tap
                                 target is not. The button is a 24px-high row
                                 with the dot drawn inside it, which is the
                                 smallest target WCAG accepts. --}}
                            <div class="flex items-center gap-2">
                                @foreach ($testimonials as $index => $testimonial)
                                    <button type="button"
                                            @click="active = {{ $index }}"
                                            :aria-current="active === {{ $index }}"
                                            class="relative flex h-6 items-center px-0.5
                                                   after:absolute after:inset-y-0 after:left-1/2 after:w-6 after:-translate-x-1/2 after:content-['']">
                                        <span :class="active === {{ $index }} ? 'w-6 bg-accent' : 'w-1.5 bg-line-strong'"
                                              class="block h-1.5 rounded-full transition-all duration-500 ease-[var(--ease-out-soft)]"></span>
                                        <span class="sr-only">Show testimonial {{ $index + 1 }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <button type="button"
                                    @click="active = (active + 1) % total"
                                    class="flex h-9 w-9 items-center justify-center rounded-full border border-line text-muted transition-colors duration-300 hover:border-line-strong hover:text-ink">
                                <span class="sr-only">Next testimonial</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ==================================================================
         The chamber

         A picture of the room, the address, and tonight's hours in one block.
         "Where is it and are you open?" is the question the home page was
         previously sending people to a second page to answer.
         ================================================================== --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">

                <div class="relative" data-reveal>
                    <img src="{{ Media::url('gallery/chamber.jpg') }}"
                         alt="Inside the chamber"
                         loading="lazy"
                         class="aspect-[4/3] w-full rounded-sm object-cover shadow-lift">

                    {{-- Today's hours, lifted onto the corner of the photo. --}}
                    <div class="absolute -bottom-6 right-4 rounded-sm border border-line bg-paper/95 px-6 py-4 shadow-lift backdrop-blur sm:-right-6">
                        <x-ui.open-status tone="soft" class="border-0 bg-transparent p-0" />
                    </div>
                </div>

                <div data-reveal="100">
                    <p class="eyebrow text-accent">The chamber</p>
                    <h2 class="mt-5 text-[1.875rem] leading-[1.12] sm:text-[2.25rem]">
                        {{ $doctor->chamber_name ?: 'Visit the chamber' }}
                    </h2>

                    <dl class="mt-9 divide-y divide-line border-y border-line">
                        @if ($doctor->fullAddress())
                            <div class="flex gap-6 py-5">
                                <dt class="eyebrow w-24 shrink-0 pt-1">Address</dt>
                                <dd class="flex-1">
                                    <address class="not-italic leading-relaxed">{{ $doctor->fullAddress() }}</address>
                                    @if ($doctor->map_latitude && $doctor->map_longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $doctor->map_latitude }},{{ $doctor->map_longitude }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-brand">
                                            Open in Maps
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg>
                                        </a>
                                    @endif
                                </dd>
                            </div>
                        @endif

                        <div class="flex gap-6 py-5">
                            <dt class="eyebrow w-24 shrink-0 pt-1">Hours</dt>
                            <dd class="flex-1">
                                <x-ui.hours-table class="-mt-3" />
                            </dd>
                        </div>

                        @if ($doctor->telHref())
                            <div class="flex gap-6 py-5">
                                <dt class="eyebrow w-24 shrink-0 pt-1">Serial</dt>
                                <dd class="flex-1">
                                    <a href="{{ $doctor->telHref() }}" class="tabular-nums text-ink transition-colors hover:text-brand">
                                        {{ $doctor->phone }}
                                    </a>
                                    <p class="mt-1 text-sm text-muted">Call during chamber hours to take a serial.</p>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <a href="{{ route('contact') }}#appointment"
                       class="mt-9 inline-flex rounded-full bg-brand px-8 py-4 text-[15px] font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
                        Request an appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         Latest articles
         ================================================================== --}}
    @if ($posts->isNotEmpty())
        <section class="border-y border-line bg-paper-shade py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <x-ui.section-heading eyebrow="Health articles" title="From the blog" :rule="false">
                    Practical guidance written for patients, not for other doctors.
                </x-ui.section-heading>

                <div class="mt-16 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $index => $post)
                        <x-ui.post-card :post="$post" :delay="$index * 80" />
                    @endforeach
                </div>

                <div class="mt-14 text-center" data-reveal>
                    <a href="{{ route('blog.index') }}"
                       class="inline-flex rounded-full border border-line-strong bg-surface px-8 py-3.5 text-sm font-medium text-ink transition-colors duration-300 hover:border-ink">
                        Read all articles
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ==================================================================
         Closing call to action
         ================================================================== --}}
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-sm bg-ink-deep px-6 py-20 text-center surface-grain sm:px-16 sm:py-24" data-reveal>
                {{-- A single wash of the accent from one corner, well under the
                     surface. Decorative. --}}
                <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-accent/15 blur-[100px]" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-2xl">
                    <h2 class="text-[1.875rem] leading-[1.12] text-white sm:text-[2.75rem]">
                        Ready to talk about your health?
                    </h2>
                    <p class="mx-auto mt-6 max-w-lg text-lg leading-relaxed text-white/65">
                        Send an appointment request and the clinic will get back to you to confirm a time that works.
                    </p>

                    <div class="mt-10 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('contact') }}#appointment"
                           class="rounded-full bg-white px-8 py-4 text-[15px] font-medium text-ink transition-colors duration-300 hover:bg-paper-shade">
                            Request an appointment
                        </a>

                        @if ($doctor->telHref())
                            <a href="{{ $doctor->telHref() }}"
                               class="rounded-full border border-white/25 px-8 py-4 text-[15px] font-medium text-white transition-colors duration-300 hover:border-white/60">
                                Call the clinic
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
