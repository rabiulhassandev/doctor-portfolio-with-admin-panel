<?php

/*
|--------------------------------------------------------------------------
| Site branding
|--------------------------------------------------------------------------
|
| >>> THIS IS THE FILE TO EDIT WHEN YOU REBRAND THE TEMPLATE FOR A NEW DOCTOR. <<<
|
| Everything that makes the site look and feel like a particular practice —
| the site name, the logo, the colour palette, the footer credit — lives here.
| Content (bio, services, blog posts, opening hours…) is managed by the doctor
| from the admin panel at /admin instead, so you should rarely need to touch
| anything outside this file and the seeders.
|
| The colour values are plain hex codes. They are injected into the page as CSS
| custom properties by resources/views/components/layouts/app.blade.php, and the
| Tailwind classes in the Blade views read those variables — so changing a hex
| code here restyles the whole public site without a rebuild.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Identity
    |--------------------------------------------------------------------------
    |
    | `name` is the browser title suffix and the navbar wordmark. It falls back
    | to APP_NAME so a buyer can also set it from .env without editing code.
    |
    */

    'name' => env('APP_NAME', 'Dr. Amelia Hart'),

    'specialization' => 'Consultant Cardiologist',

    /*
     | Path to the logo, relative to the `public/` directory. Leave it null to
     | render the doctor's initials in a coloured circle instead — which looks
     | deliberate rather than broken, so the demo site needs no artwork.
     */
    'logo' => null,

    /*
    |--------------------------------------------------------------------------
    | Colour palette
    |--------------------------------------------------------------------------
    |
    | A calm, clinical blue/teal scheme. `primary` is used for buttons, links
    | and headings; `accent` for highlights and icon backgrounds. Swap these two
    | hex codes and the entire public site follows.
    |
    */

    'colors' => [
        'primary' => '#0f5c86',        // Deep clinical blue — buttons, links, headings.
        'primary_dark' => '#0a4363',   // Hover state for primary.
        'primary_light' => '#e6f2f8',  // Tinted section backgrounds.
        'accent' => '#14a5a0',         // Teal — icon chips, underlines, highlights.
        'accent_light' => '#e4f6f5',   // Tinted accent background.
        'ink' => '#132430',            // Body copy.
        'muted' => '#5b7183',          // Secondary copy.
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults used before the doctor fills in the admin settings
    |--------------------------------------------------------------------------
    |
    | These only show on a brand-new install with an empty database. Once the
    | Doctor Profile page has been saved, the database values win.
    |
    */

    'meta_description' => 'Compassionate, evidence-based care from an experienced consultant. '
        .'Book an appointment online or visit the clinic.',

    /*
     | Pre-filled text for the floating WhatsApp button.
     */
    'whatsapp_message' => 'Hello, I would like to ask about an appointment.',

    /*
    |--------------------------------------------------------------------------
    | Feature switches
    |--------------------------------------------------------------------------
    |
    | Turn off any public section a particular buyer does not want, without
    | deleting the code (which would make future upgrades painful).
    |
    */

    'features' => [
        'blog' => true,
        'gallery' => true,
        'testimonials' => true,
        'whatsapp_button' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'blog_per_page' => 6,

    /*
    |--------------------------------------------------------------------------
    | Footer credit
    |--------------------------------------------------------------------------
    |
    | Shown in small print at the bottom of every page. Set to null to hide it.
    |
    */

    'credit' => null,

];
