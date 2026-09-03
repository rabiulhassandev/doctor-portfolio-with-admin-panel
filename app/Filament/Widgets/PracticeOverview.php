<?php

namespace App\Filament\Widgets;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Models\AppointmentRequest;
use App\Models\BlogPost;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The row of summary cards at the top of the dashboard.
 *
 * The first card is the one that matters: how many patients are waiting for an
 * answer. It links straight to the pending queue.
 */
class PracticeOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $pending = AppointmentRequest::query()->pending()->count();

        $confirmedThisMonth = AppointmentRequest::query()
            ->where('status', AppointmentStatus::Confirmed)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        return [
            Stat::make('Requests waiting for a reply', $pending)
                ->description($pending === 0
                    ? 'Nothing to do right now'
                    : 'Open the list and confirm or reject them')
                ->descriptionIcon($pending === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($pending === 0 ? 'success' : 'warning')
                ->url(AppointmentRequestResource::getUrl()),

            Stat::make('Confirmed this month', $confirmedThisMonth)
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Live on the website', Service::query()->published()->count().' services')
                ->description(BlogPost::query()->published()->count().' published articles')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('gray'),
        ];
    }
}
