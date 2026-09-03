<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\PracticeOverview;
use App\Filament\Widgets\RecentAppointmentRequests;
use App\Filament\Widgets\RequestsPerWeek;
use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The admin panel served at /admin.
 *
 * The people using this every day are the doctor and their receptionist, not
 * developers — so the sidebar is grouped into plain-English sections and the
 * dashboard leads with the only number that matters day to day: how many
 * patients are waiting for a reply.
 *
 * Colours here follow config/site.php so the panel matches the public site
 * after a rebrand.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName(config('site.name'))
            // Same typeface as the public site, self-hosted by Bunny rather
            // than Google so the panel makes no request to an ad network.
            ->font('Instrument Sans', provider: BunnyFontProvider::class)
            ->colors([
                'primary' => Color::hex(config('site.colors.primary')),
                // Filament tints its greys towards the primary hue when you
                // give it a custom grey; slate keeps long tables readable
                // without the whole panel turning blue.
                'gray' => Color::Slate,
            ])
            ->favicon(asset('favicon.ico'))
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            // Forms and tables breathe better than they do at Filament's
            // default full-bleed width on a large clinic monitor.
            ->maxContentWidth(Width::ScreenTwoExtraLarge)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            // Sections in the sidebar, in the order staff need them.
            ->navigationGroups([
                NavigationGroup::make('Patients')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make('Website content')
                    ->icon('heroicon-o-document-text'),
            ])
            // Staff constantly want to check how an edit actually looks.
            ->navigationItems([
                NavigationItem::make('View the website')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->sort(99),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                PracticeOverview::class,
                RequestsPerWeek::class,
                RecentAppointmentRequests::class,
            ])
            /*
             | The rest of the palette from config/site.php, handed to the theme
             | stylesheet as custom properties. Filament only generates
             | variables for the colours in ->colors() above, and the theme
             | needs the accent too — this keeps a rebrand to one file.
             */
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render(
                    '<style>:root{--brand-accent:{{ $accent }};--brand-ink:{{ $ink }};}</style>',
                    [
                        'accent' => config('site.colors.accent'),
                        'ink' => config('site.colors.ink'),
                    ],
                ),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
