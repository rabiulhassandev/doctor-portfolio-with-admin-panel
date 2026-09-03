<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A health article.
 *
 * "Published" means two things at once: the doctor ticked the toggle *and* the
 * publish date has arrived. {@see scopePublished()} is the single place that
 * rule lives — always use it rather than checking the columns by hand.
 *
 * @property string $title
 * @property string $slug
 * @property string|null $cover_image
 * @property string|null $excerpt
 * @property string $content
 * @property bool $is_published
 * @property Carbon|null $published_at
 * @property string|null $meta_title
 * @property string|null $meta_description
 */
class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /** Route-model binding on /blog/{post} uses the slug, not the id. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Live posts only: toggled on, and the publish date has passed. */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** Newest first — the order the blog index and previews use. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    /** The hand-written excerpt, or the first ~200 characters of the body. */
    public function excerpt(int $characters = 200): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        return Str::limit(trim(strip_tags($this->content)), $characters);
    }

    /** Rough read time, at the usual 200-words-per-minute estimate. */
    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($words / 200));
    }
}
