{{--
    "Open now · Closes 5:00 PM" — the clinic's state right now.

    Renders nothing at all when no day of the week has opening hours on it, so a
    practice that works by arrangement simply never shows the pill.

    Three tones: the default sits on a pale background, "soft" on white, and
    "inverse" on the dark footer.

    The colour is never the only signal: the words say "Open" or "Closed" too,
    which matters for the one visitor in twelve who cannot tell the dot's green
    from its amber.
--}}

@props([
    // 'light' on a pale background, 'soft' on white, 'inverse' on the footer.
    'tone' => 'light',

    // Whether to append "Closes 5:00 PM" / "Opens Monday at 9:00 AM". Turn it
    // off where the full hours are already on screen next to the pill.
    'detail' => true,
])

@php
    $status = $doctor->openStatus();
@endphp

@if ($status)
    <p {{ $attributes->class([
            'inline-flex items-center gap-2.5 rounded-full py-1.5 pl-3.5 pr-4 text-sm',
            'border border-line bg-surface/80 text-ink backdrop-blur-sm' => $tone === 'light',
            'border border-line bg-paper text-ink' => $tone === 'soft',
            'border border-white/15 bg-white/[0.06] text-white' => $tone === 'inverse',
        ]) }}>

        {{-- A soft pulse on the dot, but only while the clinic is actually
             open — a blinking light next to "Closed" reads as an alarm. --}}
        <span class="relative flex h-1.5 w-1.5 shrink-0" aria-hidden="true">
            @if ($status['is_open'])
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-60"></span>
            @endif
            <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $status['is_open'] ? 'bg-emerald-600' : 'bg-gold' }}"></span>
        </span>

        <span class="font-medium">{{ $status['label'] }}</span>

        @if ($detail)
            <span class="{{ $tone === 'inverse' ? 'text-white/60' : 'text-muted' }}">
                <span class="mr-1.5 inline-block h-3 w-px translate-y-0.5 {{ $tone === 'inverse' ? 'bg-white/20' : 'bg-line-strong' }}" aria-hidden="true"></span>
                {{ $status['detail'] }}
            </span>
        @endif
    </p>
@endif
