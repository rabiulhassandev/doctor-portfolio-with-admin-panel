<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fills a fresh install with a complete demo practice.
 *
 *     php artisan migrate:fresh --seed
 *
 * Every seeder below uses updateOrCreate, so running this again on an existing
 * database refreshes the demo content without duplicating it.
 *
 * >>> BEFORE GOING LIVE: change the admin password created here. <<<
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->createAdminUser();

        $this->call([
            // Images first — the gallery and blog seeders reference the files it writes.
            PlaceholderImageSeeder::class,
            DoctorProfileSeeder::class,
            ServiceSeeder::class,
            TestimonialSeeder::class,
            BlogPostSeeder::class,
            GalleryImageSeeder::class,
            AppointmentRequestSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('Demo practice ready.');
        $this->command->line('  Public site:  '.config('app.url'));
        $this->command->line('  Admin panel:  '.config('app.url').'/admin');
        $this->command->line('  Login:        admin@example.com / password');
        $this->command->newLine();
        $this->command->warn('Change that password before the site goes live.');
    }

    /**
     * The account that signs in to /admin.
     *
     * A buyer can add more staff logins later with `php artisan make:filament-user`.
     */
    private function createAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Clinic Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
