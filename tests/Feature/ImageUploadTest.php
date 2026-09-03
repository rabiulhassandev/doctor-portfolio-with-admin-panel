<?php

use App\Filament\Forms\Components\PhotoUpload;
use App\Support\Media;
use Database\Seeders\PlaceholderImageSeeder;
use Illuminate\Support\Facades\Storage;

/*
 | Regression cover for the day the admin panel could not edit a single picture
 | on the site.
 |
 | Two separate things caused it, and both are easy to reintroduce:
 |
 |   1. The demo images shipped as SVG. The upload field previews a file by
 |      drawing it into a canvas and hangs its crop editor off that preview, so
 |      a vector file showed a blank panel with no editor and no thumbnail.
 |
 |   2. Uploaded files were addressed with an absolute URL built from APP_URL.
 |      The upload field fetches that URL to build the preview, so on any host
 |      other than the one APP_URL happened to name the fetch never resolved and
 |      the field span forever.
 |
 | See config/filesystems.php and App\Filament\Forms\Components\PhotoUpload.
 */

it('serves uploads from a root-relative path so they resolve on any host', function () {
    expect(Media::url('gallery/clinic-1.jpg'))->toBe('/storage/gallery/clinic-1.jpg');
});

it('makes image URLs absolute for meta tags and structured data', function () {
    expect(Media::absoluteUrl('gallery/clinic-1.jpg'))
        ->toBe(url('/storage/gallery/clinic-1.jpg'))
        ->toStartWith('http');
});

it('leaves a remote image URL alone', function () {
    expect(Media::url('https://cdn.example.com/x.jpg'))->toBe('https://cdn.example.com/x.jpg')
        ->and(Media::absoluteUrl('https://cdn.example.com/x.jpg'))->toBe('https://cdn.example.com/x.jpg');
});

it('has no URL for an empty path', function () {
    expect(Media::url(null))->toBeNull()
        ->and(Media::absoluteUrl(''))->toBeNull();
});

it('refuses the image formats the upload editor cannot open', function () {
    $accepted = PhotoUpload::make('photo')->getAcceptedFileTypes();

    expect($accepted)
        ->toContain('image/jpeg', 'image/png', 'image/webp')
        ->not->toContain('image/svg+xml', 'image/*');
});

it('keeps the format restriction when the field is an avatar', function () {
    // ->avatar() re-applies Filament's image() preset, which would otherwise
    // widen the list back to image/* and let SVGs through again.
    $accepted = PhotoUpload::make('photo')->avatar()->getAcceptedFileTypes();

    expect($accepted)->not->toContain('image/*');
});

it('seeds demo images the browser can preview and the editor can crop', function () {
    Storage::fake('public');

    (new PlaceholderImageSeeder)->run();

    $files = Storage::disk('public')->allFiles();

    // Not pinned to a count — the demo photography changes. What must hold is
    // that every file arrives, and that every one of them is a raster image the
    // upload field can actually draw.
    expect($files)->not->toBeEmpty()
        ->each->toEndWith('.jpg');

    foreach (['doctor/portrait.jpg', 'gallery/chamber.jpg', 'blog/heart-symptoms.jpg'] as $expected) {
        Storage::disk('public')->assertExists($expected);
    }

    // Readable by GD is the practical definition of "the editor can open it".
    foreach ($files as $file) {
        expect(getimagesizefromstring(Storage::disk('public')->get($file)))
            ->not->toBeFalse("{$file} is not a readable image");
    }
});

it('clears vector placeholders left behind by an older install', function () {
    Storage::fake('public');
    Storage::disk('public')->put('gallery/clinic-1.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');

    (new PlaceholderImageSeeder)->run();

    Storage::disk('public')->assertMissing('gallery/clinic-1.svg');
});
