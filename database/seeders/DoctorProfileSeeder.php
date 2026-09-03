<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use Illuminate\Database\Seeder;

/**
 * The demo doctor.
 *
 * Every value here is placeholder content for the sales demo — the buyer edits
 * all of it from Admin → Doctor profile without touching this file.
 *
 * >>> Dr. Nafis Ahmed Chowdhury is a fictional person. <<<
 *
 * The demo is written for Bangladesh, so it follows how private practice
 * actually works here rather than transliterating a Western clinic: degrees in
 * the MBBS → BCS → FCPS → MD order, a BMDC registration number, a Dhanmondi
 * chamber, evening chamber hours, and Friday closed. The phone number ends in
 * zeroes and the email uses the reserved `.example` domain, so nothing here can
 * reach a real person by accident.
 */
class DoctorProfileSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate on a fixed id keeps this seeder safe to re-run.
        DoctorProfile::updateOrCreate(['id' => 1], [
            'name' => 'Dr. Nafis Ahmed Chowdhury',
            'specialization' => 'Consultant Cardiologist',

            // Invented, and deliberately not in any real BMDC range.
            'registration_label' => 'BMDC Reg. No.',
            'registration_number' => 'A-00000',
            'chamber_name' => 'Sohrid Heart Care',

            'tagline' => 'হৃদরোগের চিকিৎসা, সহজ বাংলায় — heart care explained plainly, so you leave knowing exactly what happens next.',
            'photo' => 'doctor/portrait.jpg',
            'years_of_experience' => 18,

            'short_bio' => 'I have spent eighteen years looking after hearts in Dhaka, first at the '
                .'National Institute of Cardiovascular Diseases and now from my own chamber in Dhanmondi. '
                .'Most of my work is not dramatic — it is careful listening, a clear explanation, '
                .'and a plan a family can actually afford to follow.',

            'bio' => <<<'TEXT'
            I completed my MBBS from Dhaka Medical College in 2007 and joined the BCS (Health) cadre the following year. After FCPS in Medicine and an MD in Cardiology from the National Institute of Cardiovascular Diseases, I trained in interventional cardiology in Chennai and have been practising in Dhaka ever since.

            My work covers the whole range of heart care: routine screening for people with a family history, day-to-day management of blood pressure and diabetes-related heart disease, echocardiography and ECG in the chamber, and long-term follow-up after angioplasty or bypass. I see patients from their twenties to their eighties, and the conversation matters as much as the report.

            Heart disease is now the leading cause of death in Bangladesh, and a great deal of it arrives late. Part of that is cost, part of it is that people are handed a prescription without ever being told what is actually wrong with them. I try very hard not to be that doctor. If you leave my chamber unsure what happens next, I have not done my job.

            I teach on the postgraduate cardiology programme and speak regularly on hypertension in primary care. Outside the chamber I am, less impressively, a very slow badminton player.
            TEXT,

            'philosophy' => <<<'TEXT'
            You know your body better than any report does. My starting point is always what you have noticed and what worries you — the investigations come after that, not instead of it.

            I will tell you what I think, what I am unsure about, and what the options are, in plain Bangla. Where a test can wait, I will say so; where it cannot, I will explain why. Cost is a real part of the decision and I would rather discuss it openly than have you quietly skip a medicine later.
            TEXT,

            'qualifications' => [
                ['title' => 'MBBS', 'institution' => 'Dhaka Medical College', 'year' => '2007'],
                ['title' => 'BCS (Health)', 'institution' => 'Bangladesh Civil Service', 'year' => '2008'],
                ['title' => 'FCPS (Medicine)', 'institution' => 'Bangladesh College of Physicians & Surgeons', 'year' => '2013'],
                ['title' => 'MD (Cardiology)', 'institution' => 'National Institute of Cardiovascular Diseases', 'year' => '2017'],
                ['title' => 'Fellowship, Interventional Cardiology', 'institution' => 'Madras Medical Mission, Chennai', 'year' => '2019'],
            ],

            'email' => 'appointment@sohridheartcare.example',
            'phone' => '+880 1711-000000',
            'whatsapp' => '8801711000000',

            // The chamber name lives in its own field, so it must not be
            // repeated here or every address on the site says it twice.
            'address_line' => 'House 42 (3rd floor), Road 9/A, Dhanmondi',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1209',
            'country' => 'Bangladesh',

            'map_latitude' => 23.7461,
            'map_longitude' => 90.3742,
            'map_embed_url' => null,   // Built from the coordinates above.

            /*
             | Chamber hours, not hospital hours. A consultant in Dhaka does
             | ward rounds in the morning and sits in their own chamber in the
             | evening, which is when patients actually come — so the week runs
             | Saturday to Thursday with Friday closed.
             */
            'working_hours' => [
                ['day' => 'saturday', 'opens' => '18:00', 'closes' => '21:00', 'is_closed' => false],
                ['day' => 'sunday', 'opens' => '18:00', 'closes' => '21:00', 'is_closed' => false],
                ['day' => 'monday', 'opens' => '18:00', 'closes' => '21:00', 'is_closed' => false],
                ['day' => 'tuesday', 'opens' => '18:00', 'closes' => '21:00', 'is_closed' => false],
                ['day' => 'wednesday', 'opens' => '18:00', 'closes' => '21:00', 'is_closed' => false],
                ['day' => 'thursday', 'opens' => '17:00', 'closes' => '20:00', 'is_closed' => false],
                ['day' => 'friday', 'opens' => null, 'closes' => null, 'is_closed' => true],
            ],

            'social_links' => [
                'facebook' => 'https://facebook.com/example-chamber',
                'youtube' => 'https://youtube.com/@example-chamber',
                'linkedin' => 'https://linkedin.com/in/example-chamber',
                'instagram' => null,
                'twitter' => null,
            ],

            'meta_title' => 'Dr. Nafis Ahmed Chowdhury — Cardiologist in Dhanmondi, Dhaka',
            'meta_description' => 'Consultant cardiologist in Dhanmondi, Dhaka. Echocardiography, ECG, '
                .'blood pressure and diabetes-related heart care. Serial by phone or request online.',
        ]);
    }
}
