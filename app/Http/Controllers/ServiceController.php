<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;

/** The full list of treatments and consultations. */
class ServiceController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.services', [
            'services' => Service::query()->published()->ordered()->get(),
        ]);
    }
}
