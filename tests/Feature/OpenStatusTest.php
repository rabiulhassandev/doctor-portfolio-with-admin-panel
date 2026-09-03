<?php

use App\Models\DoctorProfile;
use Illuminate\Support\Carbon;

/*
 | The "Open now / Closed now" pill in the hero, the footer and on the contact
 | page. It is the one piece of the site that changes by itself, so it is worth
 | pinning down the edges: the minute the clinic opens, the minute it shuts, and
 | the wrap from Friday evening round to Monday morning.
 */

/** A profile open 9-5 on the named weekdays and closed on the rest. */
function profileOpenOn(array $days, string $opens = '09:00', string $closes = '17:00'): DoctorProfile
{
    return new DoctorProfile([
        'name' => 'Dr. Test',
        'specialization' => 'Cardiology',
        'working_hours' => collect(DoctorProfile::DAYS)->keys()->map(fn (string $day) => [
            'day' => $day,
            'opens' => $opens,
            'closes' => $closes,
            'is_closed' => ! in_array($day, $days, true),
        ])->all(),
    ]);
}

afterEach(fn () => Carbon::setTestNow());

it('says the clinic is open during opening hours', function () {
    Carbon::setTestNow('2026-08-19 11:30:00');   // A Wednesday.

    expect(profileOpenOn(['wednesday'])->openStatus())
        ->toMatchArray(['is_open' => true, 'label' => 'Open now', 'detail' => 'Closes 5:00 PM']);
});

it('counts the opening and closing minutes as open', function (string $time) {
    Carbon::setTestNow("2026-08-19 {$time}");

    expect(profileOpenOn(['wednesday'])->openStatus()['is_open'])->toBeTrue();
})->with(['opening time' => '09:00:00', 'closing time' => '17:00:00']);

it('points at today when the clinic has not opened yet', function () {
    Carbon::setTestNow('2026-08-19 07:00:00');

    expect(profileOpenOn(['wednesday'])->openStatus())
        ->toMatchArray(['is_open' => false, 'detail' => 'Opens today at 9:00 AM']);
});

it('points at tomorrow once the clinic has shut for the day', function () {
    Carbon::setTestNow('2026-08-19 18:00:00');   // Wednesday evening.

    expect(profileOpenOn(['wednesday', 'thursday'])->openStatus())
        ->toMatchArray(['is_open' => false, 'detail' => 'Opens tomorrow at 9:00 AM']);
});

it('names the next open day when that is further off', function () {
    Carbon::setTestNow('2026-08-21 20:00:00');   // Friday night.

    expect(profileOpenOn(['monday', 'friday'])->openStatus())
        ->toMatchArray(['is_open' => false, 'detail' => 'Opens Monday at 9:00 AM']);
});

it('skips a day that is switched on but has no times on it', function () {
    Carbon::setTestNow('2026-08-19 20:00:00');   // Wednesday night.

    $profile = profileOpenOn(['thursday', 'friday']);

    // Blank out Thursday's opening time. Found by day rather than by position,
    // so reordering DoctorProfile::DAYS for another market cannot break this.
    $profile->working_hours = collect($profile->working_hours)
        ->map(fn (array $row) => $row['day'] === 'thursday' ? [...$row, 'opens' => null] : $row)
        ->all();

    expect($profile->openStatus())->toMatchArray(['detail' => 'Opens Friday at 9:00 AM']);
});

it('shows nothing at all for a practice with no opening hours', function () {
    Carbon::setTestNow('2026-08-19 11:30:00');

    expect(profileOpenOn([])->openStatus())->toBeNull();
});

it('puts the pill on the pages that promise one', function (string $url) {
    Carbon::setTestNow('2026-08-19 11:30:00');
    profileOpenOn(['wednesday'])->save();

    $this->get($url)->assertOk()->assertSee('Open now');
})->with(['home' => '/', 'contact' => '/contact']);
