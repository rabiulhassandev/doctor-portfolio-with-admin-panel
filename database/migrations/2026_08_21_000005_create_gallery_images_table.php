<?php

use App\Models\GalleryImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clinic photos shown in the lightbox grid on /gallery.
 *
 * @see GalleryImage
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');                      // Path on the `public` disk.
            $table->string('caption')->nullable();        // Shown under the image in the lightbox.
            $table->string('alt_text')->nullable();       // Accessibility; falls back to the caption.
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
