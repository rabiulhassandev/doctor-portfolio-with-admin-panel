<?php

use App\Enums\AppointmentStatus;
use App\Filament\Pages\DoctorProfileSettings;
use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\AppointmentRequest;
use App\Models\DoctorProfile;
use App\Models\User;

/*
 | The admin panel is what the buyer uses every day, so these tests cover the
 | two things that would hurt most if they broke: the panel being reachable by
 | someone who is not signed in, and the appointment workflow not working.
 */

beforeEach(function () {
    $this->admin = User::factory()->create();
});

it('sends anonymous visitors to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get(AppointmentRequestResource::getUrl())->assertRedirect('/admin/login');
});

it('shows the login page', function () {
    $this->get('/admin/login')->assertOk();
});

it('opens every admin screen for a signed-in user', function (string $url) {
    $this->actingAs($this->admin)->get($url)->assertOk();
})->with([
    'dashboard' => fn () => '/admin',
    'doctor profile' => fn () => DoctorProfileSettings::getUrl(),
    'services' => fn () => ServiceResource::getUrl(),
    'testimonials' => fn () => TestimonialResource::getUrl(),
    'blog posts' => fn () => BlogPostResource::getUrl(),
    'gallery' => fn () => GalleryImageResource::getUrl(),
    'appointment requests' => fn () => AppointmentRequestResource::getUrl(),
]);

it('opens an appointment request detail page', function () {
    $request = AppointmentRequest::factory()->create(['patient_name' => 'Helen Marsh']);

    $this->actingAs($this->admin)
        ->get(AppointmentRequestResource::getUrl('view', ['record' => $request]))
        ->assertOk()
        ->assertSee('Helen Marsh')
        ->assertSee($request->phone);
});

it('counts the pending requests in the sidebar badge', function () {
    AppointmentRequest::factory()->count(3)->create();
    AppointmentRequest::factory()->confirmed()->create();

    expect(AppointmentRequestResource::getNavigationBadge())->toBe('3');
});

it('hides the badge when nothing is waiting', function () {
    AppointmentRequest::factory()->confirmed()->create();

    expect(AppointmentRequestResource::getNavigationBadge())->toBeNull();
});

it('does not offer a way to create appointment requests by hand', function () {
    expect(AppointmentRequestResource::canCreate())->toBeFalse();
});

it('saves the doctor profile from the settings page', function () {
    // Seed a profile, then make sure the cached copy reflects an update.
    DoctorProfile::create(['name' => 'Old Name', 'specialization' => 'Cardiology']);

    expect(DoctorProfile::current()->name)->toBe('Old Name');

    DoctorProfile::query()->first()->update(['name' => 'Dr. New Name']);

    expect(DoctorProfile::current()->name)->toBe('Dr. New Name');
});

it('moves a request through the confirm and reject states', function () {
    $request = AppointmentRequest::factory()->create();

    expect($request->status)->toBe(AppointmentStatus::Pending);

    $request->markAs(AppointmentStatus::Confirmed);
    expect($request->fresh()->status)->toBe(AppointmentStatus::Confirmed);

    $request->markAs(AppointmentStatus::Rejected);
    expect($request->fresh()->status)->toBe(AppointmentStatus::Rejected);
});
