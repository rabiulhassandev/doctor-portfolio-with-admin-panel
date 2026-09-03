<?php

namespace App\Filament\Resources\AppointmentRequests\Pages;

use App\Filament\Resources\AppointmentRequests\Actions\AppointmentStatusActions;
use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Models\AppointmentRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAppointmentRequest extends ViewRecord
{
    protected static string $resource = AppointmentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AppointmentStatusActions::confirm(),
            AppointmentStatusActions::reject(),
            AppointmentStatusActions::reopen(),
            static::notesAction(),
        ];
    }

    /**
     * Staff notes are edited through a small modal rather than a full edit page,
     * because it is the only field on this record staff ever change by hand.
     */
    protected static function notesAction(): Action
    {
        return Action::make('notes')
            ->label('Staff notes')
            ->icon('heroicon-o-pencil-square')
            ->color('gray')
            ->modalWidth('lg')
            ->fillForm(fn (AppointmentRequest $record) => [
                'admin_notes' => $record->admin_notes,
            ])
            ->schema([
                Textarea::make('admin_notes')
                    ->label('Notes')
                    ->rows(5)
                    ->helperText('Private to staff — the patient never sees this.'),
            ])
            ->action(function (array $data, AppointmentRequest $record): void {
                $record->update(['admin_notes' => $data['admin_notes']]);

                Notification::make()
                    ->success()
                    ->title('Notes saved')
                    ->send();
            });
    }
}
