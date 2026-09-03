<x-layouts.app
    title="Services"
    :description="'Treatments and consultations offered by ' . $doctor->name . ', ' . $doctor->specialization . '.'">

    <x-ui.page-hero
        eyebrow="What we offer"
        title="Services"
        subtitle="Every consultation starts the same way: listening properly, explaining clearly, and agreeing a plan together." />

    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if ($services->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $index => $service)
                        {{-- Restart the stagger on each row so later cards do not
                             wait several seconds before appearing. --}}
                        <x-ui.service-card :service="$service" :delay="($index % 3) * 80" level="h2" />
                    @endforeach
                </div>
            @else
                {{-- Nothing published yet. Shows only before the doctor adds
                     services in the admin panel. --}}
                <p class="mx-auto max-w-lg text-center text-lg text-muted">
                    Service details are being updated. Please
                    <a href="{{ route('contact') }}" class="text-brand underline underline-offset-4">get in touch</a>
                    and we will be glad to help.
                </p>
            @endif
        </div>
    </section>

    {{-- Call to action --}}
    <section class="pb-20 sm:pb-28">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="border-y border-line px-6 py-20 text-center" data-reveal>
                <h2 class="text-[1.875rem] leading-[1.12] sm:text-[2.25rem]">
                    Not sure which is right for you?
                </h2>
                <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-muted">
                    Send a request with a short note about your symptoms and the clinic will point you to the right appointment.
                </p>
                <a href="{{ route('contact') }}#appointment"
                   class="mt-9 inline-flex rounded-full bg-brand px-8 py-4 text-[15px] font-medium text-white shadow-card transition-colors duration-300 hover:bg-brand-dark">
                    Request an appointment
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
