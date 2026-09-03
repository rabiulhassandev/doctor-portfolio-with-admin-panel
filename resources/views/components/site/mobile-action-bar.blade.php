{{--
    Call and Book, pinned to the bottom of the screen on phones.

    Most visits to a doctor's site come from a phone and end in one of two
    actions. Both of them otherwise live at the top of the page, so a patient
    halfway down the services list has to scroll all the way back to take
    either. This puts them permanently within thumb reach.

    Hidden from `lg` up, where the navbar already carries both.

    The matching bottom padding on <body> lives in the app layout — without it
    this bar sits on top of the last few lines of the footer.
--}}

@php
    $tel = $doctor->telHref();
@endphp

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-paper/92 backdrop-blur-md lg:hidden
            pb-[env(safe-area-inset-bottom)]">
    <div class="flex items-stretch gap-2.5 px-5 py-3">
        @if ($tel)
            <a href="{{ $tel }}"
               class="flex flex-1 items-center justify-center gap-2 rounded-full border border-line bg-surface px-4 py-3 text-sm font-medium text-ink transition-colors duration-300 hover:border-line-strong">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
                Call
            </a>
        @endif

        <a href="{{ route('contact') }}#appointment"
           class="flex flex-[1.4] items-center justify-center gap-2 rounded-full bg-brand px-4 py-3 text-sm font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            Book an appointment
        </a>
    </div>
</div>
