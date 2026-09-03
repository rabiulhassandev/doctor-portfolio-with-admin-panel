<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Demo services. The buyer replaces these from Admin → Services.
 *
 * Written for a Dhaka chamber, so the list is the one a Bangladeshi patient
 * actually recognises — echo and ECG done in the chamber the same evening,
 * diabetes-related heart care as its own line rather than a footnote, and
 * follow-up after a procedure done abroad, which is common enough here to be
 * worth naming.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Cardiac Consultation',
                'icon' => 'heroicon-o-heart',
                'short_description' => 'A full assessment of your heart, including history, examination and an ECG where needed.',
                'description' => 'Allow about 30 minutes. Please bring every medicine you are currently taking — the strips '
                    .'themselves, not a list — along with any previous reports.',
                'is_featured' => true,
            ],
            [
                'title' => 'Echocardiography (Echo)',
                'icon' => 'heroicon-o-beaker',
                'short_description' => 'Ultrasound of the heart to see how well the chambers and valves are working.',
                'description' => 'Done in the chamber and reported the same evening, so you do not have to make a second trip.',
                'is_featured' => true,
            ],
            [
                'title' => 'Blood Pressure Management',
                'icon' => 'heroicon-o-chart-bar',
                'short_description' => 'Ongoing review of high or unstable blood pressure, with medicine adjusted to suit your routine.',
                'description' => 'Includes 24-hour monitoring where a single chamber reading does not tell the whole story.',
                'is_featured' => true,
            ],
            [
                'title' => 'Diabetes & Heart Risk',
                'icon' => 'heroicon-o-shield-check',
                'short_description' => 'Combined care for patients living with diabetes, who carry a far higher risk of heart disease.',
                'description' => 'Blood sugar, cholesterol, kidney function and heart risk reviewed together rather than by '
                    .'three separate doctors who never speak to each other.',
                'is_featured' => true,
            ],
            [
                'title' => 'Preventive Heart Screening',
                'icon' => 'heroicon-o-clipboard-document-check',
                'short_description' => 'For anyone with a family history of heart disease, or who simply wants to know where they stand.',
                'description' => 'Combines blood tests, an ECG and a risk assessment into one visit, with a written plan to take home.',
                'is_featured' => false,
            ],
            [
                'title' => 'Post-Procedure Follow-Up',
                'icon' => 'heroicon-o-clock',
                'short_description' => 'Structured follow-up after angioplasty, stenting or bypass surgery — including procedures done abroad.',
                'description' => 'Bring your discharge summary and stent card. Regular reviews as you recover, coordinated with '
                    .'whoever did the procedure.',
                'is_featured' => false,
            ],
            [
                'title' => 'Tele-consultation',
                'icon' => 'heroicon-o-video-camera',
                'short_description' => 'A phone or video appointment for follow-ups, report discussions and prescription reviews.',
                'description' => 'Useful for patients outside Dhaka. If you need examining, we will arrange for you to come in instead.',
                'is_featured' => false,
            ],
        ];

        foreach ($services as $index => $service) {
            Service::updateOrCreate(
                ['slug' => str($service['title'])->slug()->value()],
                [
                    ...$service,
                    'slug' => str($service['title'])->slug()->value(),
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }
}
