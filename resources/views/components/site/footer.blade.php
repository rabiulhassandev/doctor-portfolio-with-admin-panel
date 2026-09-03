{{--
    Shared footer: contact details, quick links, opening hours and social icons.

    Set on near-black ink rather than the brand blue. A saturated blue slab at
    the bottom of every page competes with the content above it; ink lets the
    footer close the page quietly, and gives the one accent colour in here —
    the teal on the hover states — somewhere to land.
--}}

@php
    $socials = $doctor->activeSocialLinks();
    $address = $doctor->fullAddress();

    // Only the days the practice is actually open — the full table lives on /contact.
    $openDays = $doctor->scheduleRows()->reject(fn ($row) => $row['is_closed']);
@endphp

<footer class="relative mt-28 overflow-hidden bg-ink-deep text-white/65 surface-grain">
    <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8">
        <div class="grid gap-14 md:grid-cols-2 lg:grid-cols-12 lg:gap-10">

            {{-- Practice --}}
            <div class="lg:col-span-4 lg:pr-10">
                <p class="font-display text-2xl text-white">{{ $doctor->name }}</p>
                <p class="mt-2 text-[11px] uppercase tracking-[0.16em] text-accent">{{ $doctor->specialization }}</p>

                @if ($doctor->registration())
                    <p class="mt-3 text-sm tabular-nums text-white/45">{{ $doctor->registration() }}</p>
                @endif

                @if ($doctor->short_bio)
                    <p class="mt-6 text-sm leading-relaxed">
                        {{-- Cut on a word, not a character: `Str::limit` happily
                             leaves you with "…clear explanat…". --}}
                        {{ Str::words($doctor->short_bio, 24) }}
                    </p>
                @endif

                @if ($socials->isNotEmpty())
                    <ul class="mt-8 flex flex-wrap gap-2.5">
                        @foreach ($socials as $network => $url)
                            <li>
                                <a href="{{ $url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex h-10 w-10 items-center justify-center rounded-full border border-white/12 text-white/70 transition-colors duration-300 hover:border-accent hover:text-accent">
                                    <span class="sr-only">{{ ucfirst($network) }}</span>
                                    <x-site.social-icon :network="$network" class="h-4 w-4" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Quick links --}}
            <nav aria-labelledby="footer-links" class="lg:col-span-2">
                <h2 id="footer-links" class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/45">Explore</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-accent">Home</a></li>
                    <li><a href="{{ route('about') }}" class="transition-colors hover:text-accent">About</a></li>
                    <li><a href="{{ route('services') }}" class="transition-colors hover:text-accent">Services</a></li>
                    @if (config('site.features.gallery'))
                        <li><a href="{{ route('gallery') }}" class="transition-colors hover:text-accent">Gallery</a></li>
                    @endif
                    @if (config('site.features.blog'))
                        <li><a href="{{ route('blog.index') }}" class="transition-colors hover:text-accent">Health articles</a></li>
                    @endif
                    <li><a href="{{ route('contact') }}" class="transition-colors hover:text-accent">Contact</a></li>
                </ul>
            </nav>

            {{-- Contact --}}
            <div class="lg:col-span-3">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/45">Get in touch</h2>
                <ul class="mt-5 space-y-4 text-sm">
                    @if ($address)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <address class="not-italic leading-relaxed">{{ $address }}</address>
                        </li>
                    @endif

                    @if ($doctor->telHref())
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                            <a href="{{ $doctor->telHref() }}" class="tabular-nums transition-colors hover:text-accent">{{ $doctor->phone }}</a>
                        </li>
                    @endif

                    @if ($doctor->email)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:{{ $doctor->email }}" class="break-all transition-colors hover:text-accent">{{ $doctor->email }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Opening hours --}}
            <div class="lg:col-span-3">
                <h2 class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/45">Opening hours</h2>

                {{-- No "opens Monday at 9" here: the week is listed directly
                     underneath, so the pill only has to say which it is. --}}
                <x-ui.open-status tone="inverse" :detail="false" class="mt-5" />

                @if ($openDays->isNotEmpty())
                    <dl class="mt-5 space-y-2.5 text-sm">
                        @foreach ($openDays as $row)
                            <div class="flex justify-between gap-4">
                                <dt>{{ $row['label'] }}</dt>
                                <dd class="tabular-nums text-white/85">
                                    {{ \Illuminate\Support\Carbon::parse($row['opens'])->format('g:i A') }}
                                    <span class="text-white/30" aria-hidden="true">&ndash;</span>
                                    {{ \Illuminate\Support\Carbon::parse($row['closes'])->format('g:i A') }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="mt-5 text-sm">Please call to arrange an appointment.</p>
                @endif

                <a href="{{ route('contact') }}#appointment"
                   class="mt-7 inline-flex rounded-full border border-white/20 px-5 py-2.5 text-sm text-white transition-colors duration-300 hover:border-accent hover:bg-accent">
                    Request an appointment
                </a>
            </div>
        </div>

        <div class="mt-16 flex flex-col gap-2 border-t border-white/10 pt-8 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $doctor->name }}. All rights reserved.</p>
            @if (config('site.credit'))
                <p>{!! config('site.credit') !!}</p>
            @endif
        </div>
    </div>
</footer>
