<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $title = ucwords($this->faker->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'icon' => 'heroicon-o-heart',
            'short_description' => $this->faker->sentence(14),
            'description' => $this->faker->paragraphs(2, true),
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
