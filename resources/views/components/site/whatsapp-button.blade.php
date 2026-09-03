{{--
    Floating WhatsApp button, bottom-right on every page.

    Shows only when a WhatsApp number has been saved in the admin panel, so an
    empty field simply hides it rather than producing a broken link. Turn it off
    for a buyer entirely with `features.whatsapp_button` in config/site.php.
--}}

@if ($doctor->whatsappHref())
    <a href="{{ $doctor->whatsappHref() }}"
       target="_blank"
       rel="noopener noreferrer"

       {{--
            Retires when the footer comes into view.

            By then the visitor is looking at the phone number, the address and
            an appointment button, and a chat bubble floating over the top of
            them is in the way rather than to hand. It also removes the one
            collision on the site, where the button landed on the footer's own
            call to action.

            `inert` as well as opacity: a control faded to nothing is still
            focusable, and a keyboard user should not tab into something they
            cannot see.
       --}}
       x-data="{ atFooter: false }"
       x-init="
           const footer = document.querySelector('footer');
           if (footer) {
               new IntersectionObserver(
                   ([entry]) => atFooter = entry.isIntersecting,
               ).observe(footer);
           }
       "
       :class="atFooter ? 'pointer-events-none translate-y-3 opacity-0' : 'translate-y-0 opacity-100'"
       :inert="atFooter"

       {{-- Sits above the fixed call/book bar on phones, and drops back to the
            corner from `lg` up where that bar is not rendered.

            Toned down from WhatsApp's own green, which next to a warm-paper
            page looks like a sticker somebody left on the window. Ink with the
            brand mark in green reads as part of the site and is easier to find
            precisely because it is quieter than everything around it. --}}
       class="group fixed bottom-24 right-6 z-40 flex items-center gap-2.5 rounded-full bg-ink-deep/95 p-3.5 text-white shadow-float backdrop-blur
              transition-[background-color,opacity,transform] duration-500 ease-[var(--ease-out-soft)] hover:bg-ink-deep lg:bottom-8 lg:right-8 lg:pr-5"
       aria-label="Chat with us on WhatsApp">

        <svg class="h-5 w-5 shrink-0 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.47s1.06 2.86 1.21 3.06c.15.2 2.09 3.2 5.07 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35Z"/>
            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.83 9.83 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.11.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.25 8.23Z"/>
        </svg>

        {{-- On small screens the icon alone is the target, so the button stays
             out of the way of the page content. --}}
        <span class="hidden text-sm font-medium lg:inline">Chat on WhatsApp</span>
    </a>
@endif
