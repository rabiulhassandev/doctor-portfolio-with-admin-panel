<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The doctor profile is a *singleton* table: it always holds exactly one row.
 *
 * Everything that identifies the practice — the doctor's name, photo, bio,
 * contact details, opening hours and social links — lives here so the site can
 * be rebranded from the admin panel without touching a single line of code.
 *
 * @see \App\Models\DoctorProfile
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();

            // --- Identity -----------------------------------------------------
            $table->string('name');                       // "Dr. Amelia Hart"
            $table->string('specialization');             // "Consultant Cardiologist"
            $table->string('tagline')->nullable();        // Short hero strapline.
            $table->string('photo')->nullable();          // Path on the `public` disk.
            $table->unsignedSmallInteger('years_of_experience')->default(0);

            // --- Long-form content -------------------------------------------
            $table->text('short_bio')->nullable();        // 1–2 sentences, used on the home page.
            $table->longText('bio')->nullable();          // Full bio, rendered on /about.
            $table->longText('philosophy')->nullable();   // "My approach to care" section.

            /**
             * Qualifications as a JSON list so the doctor can add/remove rows in
             * the admin panel without a migration:
             * [{ "title": "MBBS", "institution": "…", "year": "2005" }, …]
             */
            $table->json('qualifications')->nullable();

            // --- Contact ------------------------------------------------------
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();       // Digits only, e.g. 14155550132.
            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Map coordinates power the schema.org markup and the map embed.
            $table->decimal('map_latitude', 10, 7)->nullable();
            $table->decimal('map_longitude', 10, 7)->nullable();
            $table->text('map_embed_url')->nullable();    // Optional Google Maps <iframe> src.

            /**
             * Opening hours as JSON, one entry per weekday:
             * [{ "day": "monday", "opens": "09:00", "closes": "17:00", "is_closed": false }, …]
             */
            $table->json('working_hours')->nullable();

            /**
             * Social profiles keyed by network:
             * { "facebook": "https://…", "instagram": "https://…", … }
             */
            $table->json('social_links')->nullable();

            // --- SEO defaults (per-page values can still override these) ------
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
