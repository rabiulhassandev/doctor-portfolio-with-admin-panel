<?php

use App\Enums\AppointmentStatus;
use App\Filament\Widgets\RecentAppointmentRequests;
use App\Filament\Widgets\RequestsPerWeek;
use App\Models\AppointmentRequest;
use App\Models\User;
use Livewire\Livewire;

/*
 | The dashboard is the first screen staff see every morning, and both of the
 | widgets on it are driven by queries that are easy to get subtly wrong — the
 | chart buckets rows by ISO week in PHP, and the queue filters and orders by
 | how long someone has been waiting. These cover both.
 */

beforeEach(function () {
    $this->admin = User::factory()->create();
});

it('hides both widgets until a request has arrived', function () {
    expect(RequestsPerWeek::canView())->toBeFalse()
        ->and(RecentAppointmentRequests::canView())->toBeFalse();

    AppointmentRequest::factory()->create();

    expect(RequestsPerWeek::canView())->toBeTrue()
        ->and(RecentAppointmentRequests::canView())->toBeTrue();
});

it('buckets enquiries into twelve weeks split by outcome', function () {
    // Two this week, one six weeks back, and one well outside the window.
    AppointmentRequest::factory()->count(2)->create();
    AppointmentRequest::factory()->confirmed()->create(['created_at' => now()->subWeeks(6)]);
    AppointmentRequest::factory()->create(['created_at' => now()->subWeeks(30)]);

    $widget = new RequestsPerWeek;
    $method = new ReflectionMethod($widget, 'getData');
    $method->setAccessible(true);
    $data = $method->invoke($widget);

    expect($data['labels'])->toHaveCount(12)
        ->and($data['datasets'])->toHaveCount(3);

    $totals = collect($data['datasets'])->mapWithKeys(
        fn (array $set) => [$set['label'] => array_sum($set['data'])],
    );

    // The 30-week-old row is outside the window, so it must not be counted.
    expect($totals['Waiting'])->toBe(2)
        ->and($totals['Confirmed'])->toBe(1)
        ->and($totals['Rejected'])->toBe(0);
});

it('lists only unanswered requests, longest wait first', function () {
    $oldest = AppointmentRequest::factory()->create([
        'patient_name' => 'Waited Longest',
        'created_at' => now()->subDays(5),
    ]);
    $newest = AppointmentRequest::factory()->create([
        'patient_name' => 'Just Arrived',
        'created_at' => now()->subMinutes(5),
    ]);
    $answered = AppointmentRequest::factory()->confirmed()->create([
        'patient_name' => 'Already Handled',
    ]);

    Livewire::actingAs($this->admin)
        ->test(RecentAppointmentRequests::class)
        ->assertCanSeeTableRecords([$oldest, $newest], inOrder: true)
        ->assertCanNotSeeTableRecords([$answered]);
});

it('mounts both widgets on the dashboard', function () {
    AppointmentRequest::factory()->create(['patient_name' => 'Nadia Okonjo']);

    // Filament renders widgets lazily, so the dashboard HTML carries the
    // Livewire components rather than their headings — assert on the mount.
    $this->actingAs($this->admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee(class_basename(RequestsPerWeek::class))
        ->assertSee(class_basename(RecentAppointmentRequests::class));
});

it('renders the enquiries chart on its own', function () {
    AppointmentRequest::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(RequestsPerWeek::class)
        ->assertOk()
        ->assertSee('Enquiries per week');
});

it('drops a request out of the queue once it is answered', function () {
    $request = AppointmentRequest::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(RecentAppointmentRequests::class)
        ->assertCanSeeTableRecords([$request]);

    $request->markAs(AppointmentStatus::Confirmed);

    Livewire::actingAs($this->admin)
        ->test(RecentAppointmentRequests::class)
        ->assertCanNotSeeTableRecords([$request]);
});
