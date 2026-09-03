<?php

namespace App\Filament\Resources\AppointmentRequests\Pages;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Models\AppointmentRequest;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListAppointmentRequests extends ListRecords
{
    protected static string $resource = AppointmentRequestResource::class;

    /**
     * Tabs across the top of the list, each with a live count.
     *
     * "Pending" is first and is where staff spend their day, so it carries a
     * count badge; the others are there for looking things up afterwards.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $countFor = fn (AppointmentStatus $status): int => AppointmentRequest::query()
            ->where('status', $status)
            ->count();

        return [
            'pending' => Tab::make('Waiting for a reply')
                ->icon('heroicon-o-clock')
                ->badge($countFor(AppointmentStatus::Pending))
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $query->where('status', AppointmentStatus::Pending)),

            'confirmed' => Tab::make('Confirmed')
                ->icon('heroicon-o-check-circle')
                ->badge($countFor(AppointmentStatus::Confirmed))
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $query->where('status', AppointmentStatus::Confirmed)),

            'rejected' => Tab::make('Rejected')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', AppointmentStatus::Rejected)),

            'all' => Tab::make('All requests')
                ->icon('heroicon-o-inbox'),
        ];
    }

    /** Open on the pending queue rather than on everything. */
    public function getDefaultActiveTab(): string
    {
        return 'pending';
    }
}
