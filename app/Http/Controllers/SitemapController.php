<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

/**
 * Generates /sitemap.xml on the fly.
 *
 * Building it per request (rather than writing a file to disk) means the doctor
 * publishes an article in the admin panel and it is in the sitemap immediately —
 * no command to run, no cron job to set up. A single-doctor site has a handful
 * of pages, so the two queries behind this cost less than reading a cached file.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('services'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.9', 'changefreq' => 'monthly'],
        ]);

        if (config('site.features.gallery')) {
            $urls->push(['loc' => route('gallery'), 'priority' => '0.6', 'changefreq' => 'monthly']);
        }

        if (config('site.features.blog')) {
            $urls->push(['loc' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'weekly']);

            BlogPost::query()
                ->published()
                ->latestFirst()
                ->get(['slug', 'updated_at', 'published_at'])
                ->each(function (BlogPost $post) use ($urls) {
                    $urls->push([
                        'loc' => route('blog.show', $post->slug),
                        'lastmod' => ($post->updated_at ?? $post->published_at)->toAtomString(),
                        'priority' => '0.7',
                        'changefreq' => 'monthly',
                    ]);
                });
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
