<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

/**
 * The landing page: hero, a short about, featured services, testimonials and
 * the latest articles.
 *
 * The doctor profile itself is shared with every view by AppServiceProvider,
 * so it is not passed here.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            // Featured services first; if the doctor hasn't marked any as
            // featured, fall back to the first few so the section is never empty.
            'services' => $this->featuredServices(),

            // The grid above shows a selection, so it cannot be counted for the
            // "services offered" figure — that has to be the real total, or the
            // page quietly understates the practice.
            'serviceCount' => Service::query()->published()->count(),

            'testimonials' => config('site.features.testimonials')
                ? Testimonial::query()->published()->ordered()->take(6)->get()
                : collect(),

            'posts' => config('site.features.blog')
                ? BlogPost::query()->published()->latestFirst()->take(3)->get()
                : collect(),
        ]);
    }

    /** Up to six services for the home-page grid. */
    private function featuredServices()
    {
        $featured = Service::query()->published()->featured()->ordered()->take(6)->get();

        return $featured->isNotEmpty()
            ? $featured
            : Service::query()->published()->ordered()->take(6)->get();
    }
}
