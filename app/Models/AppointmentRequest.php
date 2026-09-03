<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Database\Factories\AppointmentRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * An appointment enquiry submitted from the public contact form.
 *
 * The practice confirms or rejects each one by hand from the admin panel;
 * there is no live availability calendar in the Standard tier.
 *
 * @property string $patient_name
 * @property string|null $email
 * @property string $phone
 * @property Carbon $preferred_date
 * @property string $preferred_time
 * @property string|null $message
 * @property AppointmentStatus $status
 * @property string|null $admin_notes
 * @property Carbon|null $responded_at
 */
class AppointmentRequest extends Model
{
    /** @use HasFactory<AppointmentRequestFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Time slots offered on the public form. Kept here (rather than in the Blade
     * view) so the form, the validation rule and the admin filter all read the
     * same list — change a slot once and it changes everywhere.
     *
     * @var array<string, string>
     */
    public const TIME_SLOTS = [
        'morning' => 'Morning (9:00 AM – 12:00 PM)',
        'afternoon' => 'Afternoon (12:00 PM – 4:00 PM)',
        'evening' => 'Evening (4:00 PM – 8:00 PM)',
    ];

    protected function casts(): array
    {
        return [
            'status' => AppointmentStatus::class,
            'preferred_date' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AppointmentStatus::Pending);
    }

    /** Human-readable slot label, e.g. "Morning (9:00 AM – 12:00 PM)". */
    public function timeSlotLabel(): string
    {
        return self::TIME_SLOTS[$this->preferred_time] ?? $this->preferred_time;
    }

    /** Move the request to a new status and stamp when the practice responded. */
    public function markAs(AppointmentStatus $status): void
    {
        $this->update([
            'status' => $status,
            'responded_at' => now(),
        ]);
    }
}
