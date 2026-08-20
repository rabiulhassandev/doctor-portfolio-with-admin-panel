<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogPost> */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = ucfirst($this->faker->unique()->sentence(6));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'cover_image' => null,
            'excerpt' => $this->faker->sentence(20),
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(5)).'</p>',
            'is_published' => true,
            'published_at' => now()->subDays($this->faker->numberBetween(1, 90)),
        ];
    }

    /** A post saved but not yet visible on the public site. */
    public function draft(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
