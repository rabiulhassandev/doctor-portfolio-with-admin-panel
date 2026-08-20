<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AppointmentRequest> */
class AppointmentRequestFactory extends Factory
{
    protected $model = AppointmentRequest::class;

    public function definition(): array
    {
        return [
            'patient_name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('+1 415 555 ####'),
            'preferred_date' => now()->addDays($this->faker->numberBetween(1, 21))->toDateString(),
            'preferred_time' => $this->faker->randomElement(array_keys(AppointmentRequest::TIME_SLOTS)),
            'message' => $this->faker->sentence(12),
            'status' => AppointmentStatus::Pending,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => AppointmentStatus::Confirmed,
            'responded_at' => now(),
        ]);
    }
}
