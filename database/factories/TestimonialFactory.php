<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Testimonial> */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'patient_name' => $this->faker->name(),
            'patient_title' => 'Patient since '.$this->faker->numberBetween(2018, 2025),
            'photo' => null,
            'message' => $this->faker->paragraph(4),
            'rating' => 5,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
