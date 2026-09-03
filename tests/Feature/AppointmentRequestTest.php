<?php

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use App\Models\DoctorProfile;
use App\Notifications\NewAppointmentRequestNotification;
use Illuminate\Support\Facades\Notification;

/*
 | The appointment form is the one part of the public site that writes to the
 | database and sends mail, so it gets the closest attention here.
 */

beforeEach(function () {
    Notification::fake();

    DoctorProfile::create([
        'name' => 'Dr. Test Example',
        'specialization' => 'Consultant Cardiologist',
        'email' => 'clinic@example.test',
    ]);
});

/** A complete, valid submission. */
function validAppointment(array $overrides = []): array
{
    return array_merge([
        'patient_name' => 'Helen Marsh',
        'phone' => '+44 7700 900145',
        'email' => 'helen@example.test',
        'preferred_date' => now()->addWeek()->toDateString(),
        'preferred_time' => 'morning',
        'message' => 'My GP suggested I see a cardiologist.',
    ], $overrides);
}

it('saves a valid request and shows a success message', function () {
    $this->post(route('appointments.store'), validAppointment())
        ->assertRedirect(route('contact').'#appointment')
        ->assertSessionHas('appointment_submitted', true);

    $request = AppointmentRequest::sole();

    expect($request->patient_name)->toBe('Helen Marsh')
        ->and($request->status)->toBe(AppointmentStatus::Pending)
        ->and($request->preferred_time)->toBe('morning');

    // Following the redirect should surface the confirmation to the patient.
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Request sent');
});

it('emails the practice when a request comes in', function () {
    $this->post(route('appointments.store'), validAppointment());

    Notification::assertSentOnDemand(
        NewAppointmentRequestNotification::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'clinic@example.test',
    );
});

it('falls back to the configured mail address when the profile has no email', function () {
    DoctorProfile::query()->update(['email' => null]);
    DoctorProfile::forgetCurrent();

    config()->set('mail.from.address', 'fallback@example.test');

    $this->post(route('appointments.store'), validAppointment());

    Notification::assertSentOnDemand(
        NewAppointmentRequestNotification::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'fallback@example.test',
    );
});

it('requires a name, phone, date and time slot', function () {
    $this->post(route('appointments.store'), [])
        ->assertSessionHasErrors(['patient_name', 'phone', 'preferred_date', 'preferred_time']);

    expect(AppointmentRequest::count())->toBe(0);
});

it('rejects a date in the past', function () {
    $this->post(route('appointments.store'), validAppointment([
        'preferred_date' => now()->subDay()->toDateString(),
    ]))->assertSessionHasErrors('preferred_date');

    expect(AppointmentRequest::count())->toBe(0);
});

it('rejects a time slot that is not on the list', function () {
    $this->post(route('appointments.store'), validAppointment([
        'preferred_time' => 'midnight',
    ]))->assertSessionHasErrors('preferred_time');
});

it('rejects an invalid email address', function () {
    $this->post(route('appointments.store'), validAppointment([
        'email' => 'not-an-email',
    ]))->assertSessionHasErrors('email');
});

it('accepts a request with no email address', function () {
    $this->post(route('appointments.store'), validAppointment(['email' => null]))
        ->assertSessionHasNoErrors();

    expect(AppointmentRequest::sole()->email)->toBeNull();
});

it('turns away a bot that fills in the honeypot field', function () {
    $this->post(route('appointments.store'), validAppointment([
        'website' => 'http://spam.example',
    ]))->assertSessionHasErrors('website');

    expect(AppointmentRequest::count())->toBe(0);
});

it('never stores the honeypot value', function () {
    $this->post(route('appointments.store'), validAppointment(['website' => '']));

    expect(AppointmentRequest::sole()->getAttributes())->not->toHaveKey('website');
});

it('records when the practice responds', function () {
    $request = AppointmentRequest::factory()->create();

    expect($request->responded_at)->toBeNull();

    $request->markAs(AppointmentStatus::Confirmed);

    expect($request->fresh()->status)->toBe(AppointmentStatus::Confirmed)
        ->and($request->fresh()->responded_at)->not->toBeNull();
});
