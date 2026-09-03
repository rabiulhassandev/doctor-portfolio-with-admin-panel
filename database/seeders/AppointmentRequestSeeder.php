<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use Illuminate\Database\Seeder;

/**
 * A handful of demo enquiries, so the admin panel is not an empty screen on the
 * buyer's first login — and so the dashboard's "waiting for a reply" count shows
 * a real number.
 */
class AppointmentRequestSeeder extends Seeder
{
    public function run(): void
    {
        $requests = [
            [
                'patient_name' => 'Helen Marsh',
                'email' => 'helen.marsh@example.com',
                'phone' => '+44 7700 900145',
                'preferred_date' => now()->addDays(3)->toDateString(),
                'preferred_time' => 'morning',
                'message' => 'My GP suggested I see a cardiologist about some palpitations I have been getting in the evenings.',
                'status' => AppointmentStatus::Pending,
                'created_at' => now()->subHours(4),
            ],
            [
                'patient_name' => 'Ibrahim Coulibaly',
                'email' => 'i.coulibaly@example.com',
                'phone' => '+44 7700 900219',
                'preferred_date' => now()->addDays(6)->toDateString(),
                'preferred_time' => 'evening',
                'message' => 'Follow-up appointment after my procedure last month. Evenings work best around my shifts.',
                'status' => AppointmentStatus::Pending,
                'created_at' => now()->subDay(),
            ],
            [
                'patient_name' => 'Rosalind Chen',
                'email' => null,
                'phone' => '+44 7700 900377',
                'preferred_date' => now()->addDays(2)->toDateString(),
                'preferred_time' => 'afternoon',
                'message' => null,
                'status' => AppointmentStatus::Pending,
                'created_at' => now()->subDays(2),
            ],
            [
                'patient_name' => 'George Adeyemi',
                'email' => 'g.adeyemi@example.com',
                'phone' => '+44 7700 900488',
                'preferred_date' => now()->addDays(9)->toDateString(),
                'preferred_time' => 'morning',
                'message' => 'Preventive screening — my father had a heart attack at 52 and I have just turned 50.',
                'status' => AppointmentStatus::Confirmed,
                'admin_notes' => 'Confirmed by phone. Booked for 9:30am, allow 45 minutes.',
                'responded_at' => now()->subDays(3),
                'created_at' => now()->subDays(4),
            ],
            [
                'patient_name' => 'Sophie Lindqvist',
                'email' => 'sophie.l@example.com',
                'phone' => '+44 7700 900512',
                'preferred_date' => now()->subDays(2)->toDateString(),
                'preferred_time' => 'afternoon',
                'message' => 'Requested a Sunday appointment.',
                'status' => AppointmentStatus::Rejected,
                'admin_notes' => 'Clinic closed Sundays. Called back and offered Thursday evening instead.',
                'responded_at' => now()->subDays(5),
                'created_at' => now()->subDays(6),
            ],
        ];

        foreach ($requests as $request) {
            AppointmentRequest::updateOrCreate(
                [
                    'patient_name' => $request['patient_name'],
                    'preferred_date' => $request['preferred_date'],
                ],
                $request,
            );
        }
    }
}
