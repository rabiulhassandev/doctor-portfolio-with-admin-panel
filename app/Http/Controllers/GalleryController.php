<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;

/** The clinic photo grid, with an Alpine-powered lightbox. */
class GalleryController extends Controller
{
    public function __invoke(): View
    {
        // A buyer who does not want a gallery switches it off in config/site.php;
        // the route then behaves as though the page never existed.
        abort_unless(config('site.features.gallery'), 404);

        return view('pages.gallery', [
            'images' => GalleryImage::query()->published()->ordered()->get(),
        ]);
    }
}
