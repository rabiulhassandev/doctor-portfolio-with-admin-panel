@php
    use App\Models\AppointmentRequest;

    /*
     | Work out the map embed URL.
     |
     | If the doctor pasted a Google "embed" address in the admin panel we use it
     | as-is. Otherwise, if they gave coordinates, we build a standard Google
     | Maps embed from them. If neither is set, the map section is skipped.
     */
    $mapUrl = $doctor->map_embed_url;

    if (blank($mapUrl) && $doctor->map_latitude && $doctor->map_longitude) {
        $mapUrl = sprintf(
            'https://www.google.com/maps?q=%s,%s&hl=en&z=16&output=embed',
            $doctor->map_latitude,
            $doctor->map_longitude,
        );
    }

    $submitted = session('appointment_submitted');
@endphp

<x-layouts.app
    title="Contact & appointments"
    :description="'Request an appointment with ' . $doctor->name . '. Clinic address, opening hours and contact details.'">

    {{-- The Contact page is the other place Google looks for practice details. --}}
    @push('schema')
        <x-site.physician-schema />
    @endpush

    <x-ui.page-hero
        eyebrow="Get in touch"
        title="Book an appointment"
        subtitle="Send a request with the date and time that suit you. The clinic will call or email to confirm." />

    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">

                {{-- ==========================================================
                     Appointment form
                     ========================================================== --}}
                <div class="lg:col-span-7" id="appointment">
                    <div class="rounded-sm border border-line bg-surface p-7 shadow-card sm:p-10" data-reveal>

                        @if ($submitted)
                            {{--
                                Success message after the redirect. `role="status"`
                                means screen readers announce it without the user
                                having to go looking for it.
                            --}}
                            <div class="py-6 text-center"
                                 role="status"
                                 tabindex="-1"
                                 x-init="$el.focus()">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-emerald-600/25 text-emerald-700">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </span>

                                <h2 class="mt-7 text-3xl">Request sent</h2>
                                <p class="mx-auto mt-3 max-w-sm leading-relaxed text-muted">
                                    Thank you. The clinic has your request and will be in touch shortly to confirm your appointment.
                                </p>

                                <a href="{{ route('contact') }}#appointment"
                                   class="mt-8 inline-flex rounded-full border border-line px-6 py-3 text-sm font-medium text-ink transition-colors duration-300 hover:border-line-strong">
                                    Send another request
                                </a>
                            </div>
                        @else
                            <h2 class="text-3xl">Appointment request</h2>
                            <p class="mt-2 text-muted">
                                This is a request, not a confirmed booking — the clinic will contact you to agree the final time.
                            </p>

                            {{-- A summary of every error, for anyone using a screen reader. --}}
                            @if ($errors->any())
                                <div class="mt-7 rounded-lg border border-red-300/60 bg-red-50/60 p-5" role="alert">
                                    <p class="font-medium text-red-900">Please check the following:</p>
                                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-800">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST"
                                  action="{{ route('appointments.store') }}"
                                  class="mt-8 space-y-6"
                                  x-data="{ sending: false }"
                                  @submit="sending = true">
                                @csrf

                                {{--
                                    Honeypot. Hidden from people, irresistible to bots.
                                    `tabindex="-1"` and `autocomplete="off"` keep it out
                                    of the way of anyone using a keyboard or a password
                                    manager. See StoreAppointmentRequest for the rule.
                                --}}
                                <div class="hidden" aria-hidden="true">
                                    <label for="website">Leave this field empty</label>
                                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                                </div>

                                {{-- Name --}}
                                <div>
                                    <label for="patient_name" class="block text-sm font-medium text-ink">
                                        Your name <span class="text-muted" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text"
                                           name="patient_name"
                                           id="patient_name"
                                           value="{{ old('patient_name') }}"
                                           required
                                           autocomplete="name"
                                           @error('patient_name') aria-invalid="true" aria-describedby="patient_name-error" @enderror
                                           class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 placeholder:text-muted/50 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('patient_name') border-red-400 @enderror"
                                           placeholder="Jane Okafor">
                                    @error('patient_name')
                                        <p id="patient_name-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone + email --}}
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-ink">
                                            Phone <span class="text-muted" aria-hidden="true">*</span>
                                        </label>
                                        <input type="tel"
                                               name="phone"
                                               id="phone"
                                               value="{{ old('phone') }}"
                                               required
                                               autocomplete="tel"
                                               @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                                               class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 placeholder:text-muted/50 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('phone') border-red-400 @enderror"
                                               placeholder="+44 7700 900123">
                                        @error('phone')
                                            <p id="phone-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-ink">
                                            Email <span class="font-normal text-muted">(optional)</span>
                                        </label>
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               value="{{ old('email') }}"
                                               autocomplete="email"
                                               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                               class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 placeholder:text-muted/50 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('email') border-red-400 @enderror"
                                               placeholder="jane@example.com">
                                        @error('email')
                                            <p id="email-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Date + time slot --}}
                                <div class="grid gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="preferred_date" class="block text-sm font-medium text-ink">
                                            Preferred date <span class="text-muted" aria-hidden="true">*</span>
                                        </label>
                                        <input type="date"
                                               name="preferred_date"
                                               id="preferred_date"
                                               value="{{ old('preferred_date') }}"
                                               min="{{ now()->toDateString() }}"
                                               required
                                               @error('preferred_date') aria-invalid="true" aria-describedby="preferred_date-error" @enderror
                                               class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('preferred_date') border-red-400 @enderror">
                                        @error('preferred_date')
                                            <p id="preferred_date-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="preferred_time" class="block text-sm font-medium text-ink">
                                            Preferred time <span class="text-muted" aria-hidden="true">*</span>
                                        </label>
                                        <select name="preferred_time"
                                                id="preferred_time"
                                                required
                                                @error('preferred_time') aria-invalid="true" aria-describedby="preferred_time-error" @enderror
                                                class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('preferred_time') border-red-400 @enderror">
                                            <option value="">Choose a time…</option>
                                            {{-- Slots come from the model, so the form,
                                                 the validation and the admin panel can
                                                 never fall out of step. --}}
                                            @foreach (AppointmentRequest::TIME_SLOTS as $value => $label)
                                                <option value="{{ $value }}" @selected(old('preferred_time') === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('preferred_time')
                                            <p id="preferred_time-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Message --}}
                                <div>
                                    <label for="message" class="block text-sm font-medium text-ink">
                                        What would you like to discuss? <span class="font-normal text-muted">(optional)</span>
                                    </label>
                                    <textarea name="message"
                                              id="message"
                                              rows="4"
                                              maxlength="2000"
                                              @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                                              class="mt-2.5 block w-full rounded-lg border border-line bg-paper px-4 py-3.5 text-ink transition-colors duration-300 placeholder:text-muted/50 focus:border-accent focus:bg-surface focus:outline-none focus:ring-1 focus:ring-accent/40 @error('message') border-red-400 @enderror"
                                              placeholder="A short note about your symptoms or the reason for your visit.">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p id="message-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                        :disabled="sending"
                                        class="flex w-full items-center justify-center gap-2 rounded-full bg-brand px-7 py-4 text-[15px] font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-60">
                                    {{-- Swapping the label on submit stops impatient
                                         double-clicks creating two requests. --}}
                                    <span x-show="! sending">Send appointment request</span>
                                    <span x-show="sending" x-cloak class="flex items-center gap-2">
                                        <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/>
                                        </svg>
                                        Sending…
                                    </span>
                                </button>

                                <p class="text-center text-xs text-muted">
                                    In an emergency, call your local emergency number instead of using this form.
                                </p>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- ==========================================================
                     Contact details
                     ========================================================== --}}
                <aside class="space-y-8 lg:col-span-5" data-reveal="100">

                    {{-- Direct contact --}}
                    <div class="rounded-sm border border-line bg-paper-shade p-8">
                        <h2 class="text-2xl">Clinic details</h2>

                        <ul class="mt-6 space-y-5 text-sm">
                            @if ($doctor->fullAddress())
                                <li class="flex gap-4">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-accent" aria-hidden="true">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </span>
                                    <span>
                                        <span class="eyebrow">Address</span>
                                        <address class="mt-1 not-italic leading-relaxed text-muted">{{ $doctor->fullAddress() }}</address>
                                    </span>
                                </li>
                            @endif

                            @if ($doctor->telHref())
                                <li class="flex gap-4">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-accent" aria-hidden="true">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                        </svg>
                                    </span>
                                    <span>
                                        <span class="eyebrow">Phone</span>
                                        {{-- Tap to call on a phone, click to dial on a desktop. --}}
                                        <a href="{{ $doctor->telHref() }}" class="mt-1 block text-muted transition hover:text-brand">
                                            {{ $doctor->phone }}
                                        </a>
                                    </span>
                                </li>
                            @endif

                            @if ($doctor->email)
                                <li class="flex gap-4">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-accent" aria-hidden="true">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                    </span>
                                    <span>
                                        <span class="eyebrow">Email</span>
                                        <a href="mailto:{{ $doctor->email }}" class="mt-1 block break-all text-muted transition hover:text-brand">
                                            {{ $doctor->email }}
                                        </a>
                                    </span>
                                </li>
                            @endif
                        </ul>

                        @if ($doctor->whatsappHref())
                            <a href="{{ $doctor->whatsappHref() }}"
                               target="_blank" rel="noopener noreferrer"
                               class="mt-8 flex items-center justify-center gap-2.5 rounded-full border border-line bg-surface px-6 py-3.5 text-[15px] font-medium text-ink transition-colors duration-300 hover:border-line-strong">
                                {{-- The outline of the handset, not a filled
                                     blob: the button is neutral now, so the
                                     mark has to be legible on its own. --}}
                                <svg class="h-[18px] w-[18px] text-[#25D366]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.47s1.06 2.86 1.21 3.06c.15.2 2.09 3.2 5.07 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35Z"/>
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.83 9.83 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.11.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.25 8.23Z"/>
                                </svg>
                                Message on WhatsApp
                            </a>
                        @endif
                    </div>

                    {{-- Opening hours --}}
                    <div class="rounded-sm border border-line bg-surface p-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-2xl">Opening hours</h2>
                            {{-- Saves reading the whole table to answer "can I
                                 call them right now?". --}}
                            <x-ui.open-status tone="soft" />
                        </div>

                        <x-ui.hours-table class="mt-5" />
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         Map
         ================================================================== --}}
    @if ($mapUrl)
        <section class="pb-20 sm:pb-28">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="overflow-hidden rounded-sm border border-line" data-reveal>
                    {{-- `loading="lazy"` keeps the map off the critical path — it is
                         the heaviest thing on the page and sits below the fold. --}}
                    <iframe src="{{ $mapUrl }}"
                            title="Map showing the location of {{ $doctor->name }}"
                            width="100%"
                            height="420"
                            style="border:0"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="block w-full"></iframe>
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
