<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A staff login for the admin panel.
 *
 * This site has no patient accounts — the only people with a login are the
 * doctor and their reception staff. Add another with:
 *
 *     php artisan make:filament-user
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Who is allowed into /admin.
     *
     * Every row in this table is a staff account, so everyone here gets in.
     *
     * Implementing this interface is not optional: without it Filament refuses
     * access to the panel outside a local environment, and the live site would
     * answer 403 to the doctor's own login.
     *
     * If you later add non-staff users, restrict access here instead of leaving
     * it open — for example by adding an `is_admin` column:
     *
     *     return $this->is_admin;
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
