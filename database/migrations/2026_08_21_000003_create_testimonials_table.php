<?php

use App\Models\Testimonial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Patient feedback shown in the home-page slider.
 *
 * @see Testimonial
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_title')->nullable();  // e.g. "Cardiac patient, 2024".
            $table->string('photo')->nullable();          // Optional headshot on the `public` disk.
            $table->text('message');
            $table->unsignedTinyInteger('rating')->nullable(); // 1–5 stars; null hides the stars.
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
