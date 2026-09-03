<?php

use App\Filament\Pages\DoctorProfileSettings;
use App\Models\DoctorProfile;
use App\Models\User;
use Livewire\Livewire;

/*
 | The profile page is the screen the doctor uses most, and the trickiest one in
 | the panel: a singleton row, two repeaters, and social links stored as JSON.
 | These tests drive the actual Livewire component rather than the model.
 */

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('opens with the saved profile already filled in', function () {
    DoctorProfile::create([
        'name' => 'Dr. Amelia Hart',
        'specialization' => 'Consultant Cardiologist',
        'phone' => '+44 20 7946 0132',
    ]);

    Livewire::test(DoctorProfileSettings::class)
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => 'Dr. Amelia Hart',
            'specialization' => 'Consultant Cardiologist',
            'phone' => '+44 20 7946 0132',
        ]);
});

it('offers a Saturday-to-Thursday week on a brand-new install', function () {
    expect(DoctorProfile::count())->toBe(0);

    $hours = collect(DoctorProfile::defaultWorkingHours());

    // The working week in Bangladesh runs Saturday to Thursday, with Friday
    // off — so that, and not Monday to Friday, is what a blank form starts on.
    expect($hours)->toHaveCount(7)
        ->and($hours->firstWhere('day', 'saturday')['is_closed'])->toBeFalse()
        ->and($hours->firstWhere('day', 'sunday')['is_closed'])->toBeFalse()
        ->and($hours->firstWhere('day', 'friday')['is_closed'])->toBeTrue();

    // Saturday first, so the table opens on the first working day.
    expect(array_key_first(DoctorProfile::DAYS))->toBe('saturday');

    Livewire::test(DoctorProfileSettings::class)->assertOk();
});

it('creates the profile row the first time it is saved', function () {
    Livewire::test(DoctorProfileSettings::class)
        ->fillForm([
            'name' => 'Dr. New Doctor',
            'specialization' => 'General Practitioner',
            'years_of_experience' => 12,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $profile = DoctorProfile::sole();

    expect($profile->name)->toBe('Dr. New Doctor')
        ->and($profile->specialization)->toBe('General Practitioner')
        ->and($profile->years_of_experience)->toBe(12);
});

it('updates the existing row rather than creating a second one', function () {
    DoctorProfile::create(['name' => 'Old Name', 'specialization' => 'Cardiology']);

    Livewire::test(DoctorProfileSettings::class)
        ->fillForm(['name' => 'Dr. Updated Name', 'specialization' => 'Cardiology'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(DoctorProfile::count())->toBe(1)
        ->and(DoctorProfile::sole()->name)->toBe('Dr. Updated Name');
});

it('requires a name and a specialisation', function () {
    Livewire::test(DoctorProfileSettings::class)
        ->fillForm(['name' => '', 'specialization' => ''])
        ->call('save')
        ->assertHasFormErrors(['name', 'specialization']);

    expect(DoctorProfile::count())->toBe(0);
});

it('saves qualifications and opening hours as structured data', function () {
    Livewire::test(DoctorProfileSettings::class)
        ->fillForm([
            'name' => 'Dr. Amelia Hart',
            'specialization' => 'Consultant Cardiologist',
            'qualifications' => [
                ['title' => 'MBChB', 'institution' => 'Edinburgh', 'year' => '2005'],
                ['title' => 'MRCP', 'institution' => 'RCP', 'year' => '2009'],
            ],
            'working_hours' => [
                ['day' => 'monday', 'opens' => '09:00', 'closes' => '17:30', 'is_closed' => false],
                ['day' => 'sunday', 'opens' => null, 'closes' => null, 'is_closed' => true],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $profile = DoctorProfile::sole();

    expect($profile->qualifications)->toHaveCount(2)
        ->and($profile->qualifications[0]['title'])->toBe('MBChB')
        ->and(collect($profile->working_hours)->firstWhere('day', 'monday')['opens'])->toBe('09:00');
});

it('saves social links as JSON and hides the empty ones', function () {
    Livewire::test(DoctorProfileSettings::class)
        ->fillForm([
            'name' => 'Dr. Amelia Hart',
            'specialization' => 'Consultant Cardiologist',
            'social_links' => [
                'facebook' => 'https://facebook.com/example',
                'instagram' => null,
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $links = DoctorProfile::sole()->activeSocialLinks();

    expect($links->toArray())->toBe(['facebook' => 'https://facebook.com/example']);
});

it('shows the new details on the public site straight after saving', function () {
    DoctorProfile::create(['name' => 'Old Name', 'specialization' => 'Cardiology']);

    Livewire::test(DoctorProfileSettings::class)
        ->fillForm(['name' => 'Dr. Brand New', 'specialization' => 'Cardiology'])
        ->call('save');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Dr. Brand New')
        ->assertDontSee('Old Name');
});
