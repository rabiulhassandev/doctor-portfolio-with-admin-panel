<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;

/** The About page: full biography, qualifications and approach to care. */
class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'services' => Service::query()->published()->ordered()->take(4)->get(),
        ]);
    }
}
