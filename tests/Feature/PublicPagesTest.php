<?php

use App\Models\BlogPost;
use App\Models\DoctorProfile;
use App\Models\GalleryImage;
use App\Models\Service;
use App\Models\Testimonial;

/*
 | Smoke tests for the public site: every page loads, and each one shows the
 | content it is supposed to show.
 */

beforeEach(function () {
    $this->profile = DoctorProfile::create([
        'name' => 'Dr. Test Example',
        'specialization' => 'Consultant Cardiologist',
        'phone' => '+44 20 7946 0132',
        'email' => 'clinic@example.test',
        'address_line' => '48 Harley Street',
        'city' => 'London',
        'working_hours' => DoctorProfile::defaultWorkingHours(),
    ]);
});

it('shows the home page with the doctor details', function () {
    $service = Service::factory()->create(['title' => 'Cardiac Consultation', 'is_featured' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Dr. Test Example')
        ->assertSee('Consultant Cardiologist')
        ->assertSee($service->title);
});

it('shows the about page with the qualifications', function () {
    $this->profile->update([
        'bio' => 'A long biography.',
        'qualifications' => [['title' => 'MBChB', 'institution' => 'Edinburgh', 'year' => '2005']],
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('A long biography.')
        ->assertSee('MBChB');
});

it('lists every published service', function () {
    Service::factory()->create(['title' => 'Published Service']);
    Service::factory()->create(['title' => 'Hidden Service', 'is_published' => false]);

    $this->get(route('services'))
        ->assertOk()
        ->assertSee('Published Service')
        ->assertDontSee('Hidden Service');
});

it('shows published gallery photos', function () {
    GalleryImage::factory()->create(['caption' => 'Reception desk']);
    GalleryImage::factory()->create(['caption' => 'Hidden photo', 'is_published' => false]);

    $this->get(route('gallery'))
        ->assertOk()
        ->assertSee('Reception desk')
        ->assertDontSee('Hidden photo');
});

it('shows the contact page with the opening hours and the form', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Appointment request')
        ->assertSee('Opening hours')
        ->assertSee('48 Harley Street');
});

it('renders testimonials on the home page', function () {
    Testimonial::factory()->create(['patient_name' => 'Margaret Whitfield']);
    Testimonial::factory()->create(['patient_name' => 'Hidden Patient', 'is_published' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Margaret Whitfield')
        ->assertDontSee('Hidden Patient');
});

it('serves a valid sitemap listing the published posts', function () {
    $live = BlogPost::factory()->create(['slug' => 'live-post']);
    $draft = BlogPost::factory()->draft()->create(['slug' => 'draft-post']);

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');

    expect($response->getContent())
        ->toContain(route('blog.show', $live->slug))
        ->not->toContain(route('blog.show', $draft->slug));
});

it('hides the gallery when the feature is switched off', function () {
    config()->set('site.features.gallery', false);

    $this->get(route('gallery'))->assertNotFound();
});
