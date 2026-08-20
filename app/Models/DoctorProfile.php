<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Singleton model holding every piece of doctor-specific content.
 *
 * There is only ever one row. Read it with {@see DoctorProfile::current()},
 * which caches the instance for the lifetime of the request so a page that
 * renders the navbar, footer and hero doesn't hit the database three times.
 *
 * @property string $name
 * @property string $specialization
 * @property string|null $tagline
 * @property string|null $photo
 * @property int $years_of_experience
 * @property string|null $short_bio
 * @property string|null $bio
 * @property string|null $philosophy
 * @property array|null $qualifications
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $whatsapp
 * @property string|null $address_line
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property float|null $map_latitude
 * @property float|null $map_longitude
 * @property string|null $map_embed_url
 * @property array|null $working_hours
 * @property array|null $social_links
 * @property string|null $meta_title
 * @property string|null $meta_description
 */
class DoctorProfile extends Model
{
    /** Weekday keys, in display order. Used by the admin form and the hours table. */
    public const DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    /** Mass assignment is safe here: the only writer is the admin settings page. */
    protected $guarded = [];

    /** Request-scoped cache for {@see self::current()}. */
    protected static ?self $currentInstance = null;

    protected function casts(): array
    {
        return [
            'qualifications' => 'array',
            'working_hours' => 'array',
            'social_links' => 'array',
            'years_of_experience' => 'integer',
            'map_latitude' => 'float',
            'map_longitude' => 'float',
        ];
    }

    /**
     * The one and only profile row.
     *
     * If the table is empty (fresh install, before seeding) this returns an
     * unsaved model populated from config/site.php, so the public pages still
     * render instead of blowing up on null.
     */
    public static function current(): self
    {
        return static::$currentInstance ??= static::query()->first() ?? new static([
            'name' => config('site.name'),
            'specialization' => config('site.specialization'),
            'working_hours' => static::defaultWorkingHours(),
        ]);
    }

    /** Forget the cached instance — call this after saving in the admin panel. */
    public static function forgetCurrent(): void
    {
        static::$currentInstance = null;
    }

    /** A sensible Mon–Fri 9–5 schedule used as the form default on a fresh install. */
    public static function defaultWorkingHours(): array
    {
        return collect(self::DAYS)
            ->map(fn (string $label, string $key) => [
                'day' => $key,
                'opens' => '09:00',
                'closes' => '17:00',
                'is_closed' => in_array($key, ['saturday', 'sunday'], true),
            ])
            ->values()
            ->all();
    }

    /**
     * Working hours normalised to one entry per weekday, in Monday-first order,
     * so the public hours table never has gaps even if the JSON is incomplete.
     *
     * @return Collection<int, array{day: string, label: string, opens: ?string, closes: ?string, is_closed: bool, is_today: bool}>
     */
    public function scheduleRows(): Collection
    {
        $saved = collect($this->working_hours ?? [])->keyBy('day');
        $today = strtolower(Carbon::now()->format('l'));

        return collect(self::DAYS)->map(function (string $label, string $key) use ($saved, $today) {
            $row = $saved->get($key, []);

            return [
                'day' => $key,
                'label' => $label,
                'opens' => $row['opens'] ?? null,
                'closes' => $row['closes'] ?? null,
                'is_closed' => (bool) ($row['is_closed'] ?? true),
                'is_today' => $key === $today,
            ];
        })->values();
    }

    /** Street address on one line, e.g. "12 Harley St, London, NW1 4LT, UK". */
    public function fullAddress(): string
    {
        return collect([
            $this->address_line,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ])->filter()->implode(', ');
    }

    /** Social profile URLs that were actually filled in, keyed by network. */
    public function activeSocialLinks(): Collection
    {
        return collect($this->social_links ?? [])->filter(fn ($url) => filled($url));
    }

    /** `tel:` href with spaces and punctuation stripped. */
    public function telHref(): ?string
    {
        return filled($this->phone)
            ? 'tel:'.preg_replace('/[^0-9+]/', '', $this->phone)
            : null;
    }

    /** Pre-filled WhatsApp deep link for the floating chat button. */
    public function whatsappHref(): ?string
    {
        if (blank($this->whatsapp)) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
        $message = rawurlencode(config('site.whatsapp_message'));

        return "https://wa.me/{$number}?text={$message}";
    }
}
