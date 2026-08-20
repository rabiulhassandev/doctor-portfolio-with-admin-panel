<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Health articles written by the doctor.
 *
 * A post is publicly visible only when `is_published` is true AND `published_at`
 * is in the past — that pairing lets the doctor schedule posts ahead of time.
 *
 * @see \App\Models\BlogPost
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();    // Path on the `public` disk.
            $table->string('excerpt', 500)->nullable();   // Falls back to a trimmed body.
            $table->longText('content');                  // Rich text (HTML) from Filament.

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // Per-post SEO overrides; blank values fall back to the title/excerpt.
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
