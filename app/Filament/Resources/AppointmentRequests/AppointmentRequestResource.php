<?php

namespace App\Filament\Resources\AppointmentRequests;

use App\Filament\Resources\AppointmentRequests\Pages\ListAppointmentRequests;
use App\Filament\Resources\AppointmentRequests\Pages\ViewAppointmentRequest;
use App\Filament\Resources\AppointmentRequests\Schemas\AppointmentRequestInfolist;
use App\Filament\Resources\AppointmentRequests\Tables\AppointmentRequestsTable;
use App\Models\AppointmentRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * The screen the practice lives in day to day: enquiries sent from the public
 * contact form, waiting to be confirmed or turned down.
 *
 * There is deliberately no "create" page — requests only ever arrive from
 * patients — and no edit form either. Staff act on a request through the
 * Confirm / Reject buttons, which keeps the daily workflow to one click.
 */
class AppointmentRequestResource extends Resource
{
    protected static ?string $model = AppointmentRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Patients';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Appointment requests';

    protected static ?string $modelLabel = 'appointment request';

    protected static ?string $recordTitleAttribute = 'patient_name';

    /** Number of unanswered requests, shown as a badge next to the sidebar link. */
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()->pending()->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Requests still waiting for a reply';
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppointmentRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentRequestsTable::configure($table);
    }

    /** Patients create these from the website, never staff. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointmentRequests::route('/'),
            'view' => ViewAppointmentRequest::route('/{record}'),
        ];
    }
}
