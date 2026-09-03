<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Treatments / consultations the doctor offers.
 *
 * @see Service
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();             // Stable anchor + future detail pages.

            /**
             * A Heroicon name (e.g. "heroicon-o-heart") so the doctor can pick an
             * icon from a dropdown instead of uploading artwork.
             */
            $table->string('icon')->default('heroicon-o-heart');

            $table->string('short_description', 500);     // Card blurb.
            $table->text('description')->nullable();      // Optional longer copy.

            $table->boolean('is_featured')->default(false); // Shown in the home-page preview.
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // The public site always filters by `is_published` and orders by `sort_order`.
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
