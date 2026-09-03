<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;

/** The blog index and the individual article page. */
class BlogController extends Controller
{
    /** Paginated list of live articles, newest first. */
    public function index(): View
    {
        abort_unless(config('site.features.blog'), 404);

        return view('pages.blog.index', [
            'posts' => BlogPost::query()
                ->published()
                ->latestFirst()
                ->paginate(config('site.blog_per_page'))
                ->withQueryString(),
        ]);
    }

    /**
     * One article.
     *
     * The route binds by slug; scoping to published() here means a draft URL
     * returns a 404 even if somebody guesses or shares the address.
     */
    public function show(string $slug): View
    {
        abort_unless(config('site.features.blog'), 404);

        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.blog.show', [
            'post' => $post,
            // A few more to read, excluding the one already open.
            'related' => BlogPost::query()
                ->published()
                ->whereKeyNot($post->getKey())
                ->latestFirst()
                ->take(2)
                ->get(),
        ]);
    }
}
