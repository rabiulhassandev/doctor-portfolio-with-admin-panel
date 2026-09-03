<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
|
| Every page a patient can see. The admin panel is not listed here — Filament
| registers /admin itself from app/Providers/Filament/AdminPanelProvider.php.
|
| Route names (home, about, services…) are used throughout the Blade views, so
| if you rename a URL below the whole site follows automatically.
|
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/services', ServiceController::class)->name('services');
Route::get('/gallery', GalleryController::class)->name('gallery');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

/*
 | The appointment request form posts here. Throttled to six submissions per
 | minute from one IP — enough for a patient who mistypes something, not enough
 | for a script to flood the practice's inbox.
 */
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('appointments.store');

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
