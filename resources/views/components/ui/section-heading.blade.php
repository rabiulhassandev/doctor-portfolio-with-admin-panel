{{--
    The eyebrow + heading + lead paragraph that opens most sections.

        <x-ui.section-heading eyebrow="What we treat" title="Our services">
            Optional lead paragraph.
        </x-ui.section-heading>

    There used to be a short accent bar under every heading on the site. Six
    identical coloured dashes down one page is decoration by habit rather than
    by intent, so the rule is now a hairline that runs the width of the block
    and sits *above* the eyebrow — it separates sections instead of underlining
    them, which is the job it was actually doing.
--}}

@props([
    'eyebrow' => null,
    'title',
    'align' => 'center',   // center | start
    'level' => 'h2',       // Use h1 only once per page.
    'rule' => true,        // The hairline above the eyebrow.
])

@php
    $isCentered = $align === 'center';
@endphp

<div {{ $attributes->class([
        'max-w-2xl',
        'mx-auto text-center' => $isCentered,
    ]) }} data-reveal>

    @if ($rule)
        <span class="block h-px w-full bg-line" aria-hidden="true"></span>
    @endif

    @if ($eyebrow)
        <p class="eyebrow {{ $rule ? 'mt-6' : '' }}">{{ $eyebrow }}</p>
    @endif

    <{{ $level }} class="mt-5 text-[1.875rem] leading-[1.12] sm:text-[2.5rem]">
        {{ $title }}
    </{{ $level }}>

    @if (trim($slot) !== '')
        <p class="mt-5 text-lg leading-relaxed text-muted {{ $isCentered ? 'mx-auto max-w-xl' : 'max-w-xl' }}">
            {{ $slot }}
        </p>
    @endif
</div>
