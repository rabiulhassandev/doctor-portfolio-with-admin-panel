<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

/**
 * Demo chamber photos.
 *
 * The files themselves are copied onto the disk by {@see PlaceholderImageSeeder},
 * which must run first. They are stock photographs, not pictures of any real
 * chamber — replace all six from Admin → Gallery before launch.
 */
class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            [
                'image' => 'gallery/chamber.jpg',
                'caption' => 'The chamber',
                'alt_text' => 'The consultation chamber, with a desk, two chairs and an examination couch',
            ],
            [
                'image' => 'gallery/reception.jpg',
                'caption' => 'Reception',
                'alt_text' => 'The reception counter where serials are taken and reports collected',
            ],
            [
                'image' => 'gallery/waiting-area.jpg',
                'caption' => 'Waiting area',
                'alt_text' => 'Seating in the waiting area outside the chamber',
            ],
            [
                'image' => 'gallery/echocardiography.jpg',
                'caption' => 'Echocardiography',
                'alt_text' => 'The echocardiography machine used for heart ultrasound scans',
            ],
            [
                'image' => 'gallery/ecg-room.jpg',
                'caption' => 'ECG room',
                'alt_text' => 'A technician reviewing an ECG trace on screen in the ECG room',
            ],
            [
                'image' => 'gallery/procedure-room.jpg',
                'caption' => 'Procedure room',
                'alt_text' => 'The procedure room, laid out with clinical equipment',
            ],
        ];

        foreach ($images as $index => $image) {
            /*
             | Matched on the caption rather than the file, so re-seeding an
             | install that already has these rows re-points them at the current
             | photograph instead of leaving six orphans behind next to six new
             | ones. The captions are the stable identity here; the filenames
             | have already changed twice.
             */
            GalleryImage::updateOrCreate(
                ['caption' => $image['caption']],
                [
                    ...$image,
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }
}
