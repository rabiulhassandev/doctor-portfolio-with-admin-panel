<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GalleryImage> */
class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        return [
            'image' => 'gallery/placeholder.svg',
            'caption' => ucfirst($this->faker->words(3, true)),
            'alt_text' => null,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
