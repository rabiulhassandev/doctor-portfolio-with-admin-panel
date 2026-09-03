<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * Demo articles.
 *
 * The `content` field holds HTML because that is what Filament's rich text
 * editor produces — the doctor writes in a normal editor and never sees tags.
 * One post is left as a draft so the buyer can see the published/draft states
 * side by side in the admin list.
 *
 * Written for Bangladeshi readers: the examples are local food, local costs and
 * the local habit of buying medicine off a pharmacy counter, because generic
 * heart advice translated from a Western site is exactly what patients here
 * already ignore.
 */
class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Five heart symptoms worth getting checked',
                'slug' => 'five-heart-symptoms-worth-getting-checked',
                'cover_image' => 'blog/heart-symptoms.jpg',
                'excerpt' => 'Most chest pain is not the heart. Some of it is. Here is how to tell the difference '
                    .'without spending your evening on Google.',
                'content' => <<<'HTML'
                <p>Almost every evening someone sits down in my chamber and opens with the same sentence: "It is probably gas, but…". Usually they are right. Occasionally they are not, and the difference is worth knowing.</p>

                <h2>1. Chest discomfort that comes on with effort</h2>
                <p>Pain or tightness that appears when you walk up a flight of stairs or hurry for a bus, and settles when you stop, is the pattern that concerns a cardiologist most. Patients almost never describe it as pain — they say <em>bhar lage</em>, a heaviness, or a band tightening across the chest.</p>
                <p>Sharp pain that changes when you breathe in, or that you can reproduce by pressing on the spot, is much less likely to be coming from your heart.</p>

                <h2>2. Breathlessness that is new</h2>
                <p>Getting winded on the stairs when you did not use to is worth mentioning, particularly if it has appeared over weeks rather than years. So is waking at night needing to sit up, or finding you now need two pillows where one used to do.</p>

                <h2>3. Palpitations with dizziness</h2>
                <p>A racing heart on its own is usually not dangerous, and is very often anxiety or too much tea. A racing heart together with light-headedness, or one that has made you faint, needs an ECG.</p>

                <h2>4. Swelling in both ankles</h2>
                <p>Ankles that swell through the day and settle overnight have many causes, and most are not cardiac. But when both sides swell and it comes with breathlessness, the heart is worth ruling out.</p>

                <h2>5. Pain that spreads</h2>
                <p>Discomfort travelling into the jaw, the throat, the left arm or between the shoulder blades — especially with sweating or nausea — should not wait until morning. Go to the nearest emergency department.</p>

                <h2>What to do</h2>
                <p>None of these five means you are having a heart attack. All five mean the question deserves a proper answer rather than a guess. An ECG costs a few hundred taka and takes five minutes, and it is a far better use of an evening than a symptom checker.</p>
                HTML,
                'days_ago' => 6,
                'is_published' => true,
            ],

            [
                'title' => 'What your blood pressure numbers actually mean',
                'slug' => 'what-your-blood-pressure-numbers-actually-mean',
                'cover_image' => 'blog/blood-pressure.jpg',
                'excerpt' => 'Two numbers, endless confusion. A short guide to what the top and bottom figures '
                    .'describe, and why one high reading at the pharmacy is rarely worth losing sleep over.',
                'content' => <<<'HTML'
                <p>Blood pressure is written as two numbers because your arteries are under two different pressures: one while the heart squeezes, one while it refills. 120/80 means 120 during the squeeze and 80 in between.</p>

                <h2>The top number</h2>
                <p>Systolic pressure — the squeeze. It rises with age in almost everybody, and it is the number that predicts stroke risk most strongly after about fifty.</p>

                <h2>The bottom number</h2>
                <p>Diastolic pressure — the resting pressure between beats. In younger patients this is often the number that goes wrong first.</p>

                <h2>Why one reading means very little</h2>
                <p>Blood pressure is not a fixed property of a person. It moves with the time of day, with how you slept, with the last cup of tea, and — reliably — with sitting in a doctor's chamber at all. A single high reading taken at a pharmacy counter after fighting through Dhaka traffic is not a diagnosis.</p>
                <p>What matters is the pattern. If you can, buy a digital machine, sit quietly for five minutes, and take a reading morning and evening for a week. Write them down. That page is worth more to me than anything I can measure in three minutes in the chamber.</p>

                <h2>When to act</h2>
                <p>Readings consistently above 140/90 across a week deserve a proper review. Above 180/110, or any high reading with chest pain, breathlessness, severe headache or visual disturbance, needs attention the same day.</p>

                <h2>One warning</h2>
                <p>Please do not buy or stop pressure medicine on a pharmacy counter's advice, and do not stop it because you feel fine — feeling fine is what successful treatment is supposed to feel like.</p>
                HTML,
                'days_ago' => 21,
                'is_published' => true,
            ],

            [
                'title' => 'Reading your echo report without panicking',
                'slug' => 'reading-your-echo-report-without-panicking',
                'cover_image' => 'blog/echo-report.jpg',
                'excerpt' => 'Ejection fraction, mild regurgitation, grade I diastolic dysfunction. What the '
                    .'phrases on an echocardiogram report mean, and which ones are genuinely normal.',
                'content' => <<<'HTML'
                <p>An echocardiogram report is written by one specialist for another, which is why handing it to the patient at the counter causes so much unnecessary fear. Here is what the common phrases actually mean.</p>

                <h2>Ejection fraction</h2>
                <p>The percentage of blood the main chamber pushes out with each beat. Anything from about 55% upwards is normal. It is not a school mark — 60% is not better than 58%, and a healthy heart never approaches 100%.</p>

                <h2>Mild regurgitation</h2>
                <p>A small amount of backward leak across a valve. Mild mitral or tricuspid regurgitation appears on a great many perfectly normal reports and usually needs nothing at all beyond a repeat scan in a few years.</p>

                <h2>Grade I diastolic dysfunction</h2>
                <p>The heart is slightly stiffer than ideal while relaxing. It is extremely common over forty, especially with long-standing high blood pressure, and on its own it is a reason to control your pressure well — not a reason to be frightened.</p>

                <h2>What actually needs attention</h2>
                <p>A low ejection fraction, moderate or severe valve disease, or a chamber that has become noticeably enlarged. Those findings change what we do next, and I will tell you plainly if any of them appear on your report.</p>

                <h2>Bring the report, not the WhatsApp photograph</h2>
                <p>The images matter as much as the text, and a screenshot of a screenshot loses them. Please bring the printed report and the CD if one was given to you.</p>
                HTML,
                'days_ago' => 40,
                'is_published' => true,
            ],

            [
                'title' => 'Salt, oil and the Bengali kitchen',
                'slug' => 'salt-oil-and-the-bengali-kitchen',
                'cover_image' => null,
                'excerpt' => 'You do not have to give up the food you grew up on. A few practical changes to '
                    .'how it is cooked will do more for your pressure than any list of forbidden dishes.',
                'content' => <<<'HTML'
                <p>Every patient I put on blood pressure medicine asks the same question, and it is a fair one: what am I allowed to eat? The honest answer is that almost nothing is forbidden, and that how a dish is cooked matters far more than whether it appears on some list.</p>

                <h2>The salt is mostly not in the salt pot</h2>
                <p>Most of the salt in a Bangladeshi diet arrives before the food reaches the table. Pickles, shutki, processed snacks, restaurant food and the salt already in the cooking are where it accumulates.</p>

                <h2>Three changes that work</h2>
                <p>Take the salt pot off the dining table. Halve the salt in cooking over a month rather than overnight — the palate adjusts, and nobody notices a gradual change. Rinse tinned and packet food before it goes in the pan.</p>

                <h2>Oil</h2>
                <p>Reuse of frying oil is the habit I would most like to see end. Beyond that, the quantity matters more than the brand: the same curry cooked in half the oil is the single easiest change most families can make.</p>
                HTML,
                'days_ago' => null,   // Draft: written but not published yet.
                'is_published' => false,
            ],
        ];

        foreach ($posts as $post) {
            $daysAgo = $post['days_ago'];
            unset($post['days_ago']);

            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                [
                    ...$post,
                    'published_at' => $daysAgo === null ? null : now()->subDays($daysAgo),
                ],
            );
        }
    }
}
