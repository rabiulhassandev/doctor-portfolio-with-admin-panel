<?php

namespace App\Models;

use Database\Factories\GalleryImageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A clinic photo shown in the /gallery lightbox grid.
 *
 * @property string $image
 * @property string|null $caption
 * @property string|null $alt_text
 * @property bool $is_published
 * @property int $sort_order
 */
class GalleryImage extends Model
{
    /** @use HasFactory<GalleryImageFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Alt text for the <img> tag, never empty: falls back to the caption and
     * then to a generic description so the page stays accessible.
     */
    public function altText(): string
    {
        return $this->alt_text
            ?: $this->caption
            ?: 'Photograph of the clinic';
    }
}
