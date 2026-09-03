<?php

namespace App\Providers;

use App\Models\DoctorProfile;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         | Every public page — navbar, footer, hero, contact details — needs the
         | doctor's profile. Sharing it here means no controller has to remember
         | to pass it, and DoctorProfile::current() caches it so this costs one
         | query per request no matter how many views use it.
         |
         | Views refer to it simply as $doctor.
         */
        View::composer('*', function ($view) {
            $view->with('doctor', DoctorProfile::current());
        });

        // Tailwind-friendly pagination markup for the blog list.
        Paginator::useTailwind();
    }
}
