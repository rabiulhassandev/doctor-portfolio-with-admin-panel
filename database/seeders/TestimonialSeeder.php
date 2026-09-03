<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Demo patient quotes.
 *
 * These are invented for the demo. Replace them before launch — and get written
 * permission from real patients before publishing anything they said.
 *
 * No photographs, deliberately. Attaching a stranger's face to words they never
 * said is bad enough on its own; doing it with a claim about their heart is
 * worse. The site falls back to initials in a circle, which is also what most
 * chambers want anyway — patients rarely thank you for putting their face on a
 * page about their cardiac history.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'patient_name' => 'Rezaul Karim',
                'patient_title' => 'Patient since 2021',
                'rating' => 5,
                'message' => 'For two years I was told my chest pain was gas. Sir was the first one to actually do an ECG '
                    .'and an echo the same evening and explain what he was seeing on the screen. He drew it on paper for '
                    .'my son so we both understood.',
            ],
            [
                'patient_name' => 'Shirin Akter',
                'patient_title' => 'Blood pressure management',
                'rating' => 5,
                'message' => 'My pressure had been uncontrolled for a long time and every doctor just added another tablet. '
                    .'He took away two of them and changed one, and asked about my sleep and my work. It has been steady '
                    .'since then.',
            ],
            [
                'patient_name' => 'Abdul Mannan',
                'patient_title' => 'Follow-up after stenting',
                'rating' => 5,
                'message' => 'I had my stent done in Chennai and did not know who to follow up with here. He read the whole '
                    .'discharge summary properly and told me honestly which medicines I would need for life and which I '
                    .'could stop. Very few doctors give you that much time.',
            ],
            [
                'patient_name' => 'Farhana Islam',
                'patient_title' => 'Preventive screening',
                'rating' => 4,
                'message' => 'My father died of a heart attack at fifty-two, so I came out of fear more than anything. I got '
                    .'a proper risk assessment and honest reassurance where it was warranted, and a clear list of what to '
                    .'change. He also told me what I did not need to spend money on.',
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::updateOrCreate(
                ['patient_name' => $testimonial['patient_name']],
                [
                    ...$testimonial,
                    'photo' => null,
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }
}
