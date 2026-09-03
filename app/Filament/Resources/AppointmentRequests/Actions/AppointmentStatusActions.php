<?php

namespace App\Filament\Resources\AppointmentRequests\Actions;

use App\Enums\AppointmentStatus;
use App\Models\AppointmentRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * The Confirm / Reject buttons.
 *
 * They are defined once here and reused by both the list table and the detail
 * page, so the two screens can never drift apart.
 */
class AppointmentStatusActions
{
    /** Mark a request as confirmed. Hidden once the request already is. */
    public static function confirm(): Action
    {
        return Action::make('confirm')
            ->label('Confirm')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            // A confirmation step: staff work quickly and a mis-click here would
            // tell a patient the wrong thing.
            ->requiresConfirmation()
            ->modalHeading('Confirm this appointment?')
            ->modalDescription('Remember to call or email the patient to let them know.')
            ->modalSubmitActionLabel('Yes, confirm it')
            ->visible(fn (AppointmentRequest $record) => $record->status !== AppointmentStatus::Confirmed)
            ->action(function (AppointmentRequest $record): void {
                $record->markAs(AppointmentStatus::Confirmed);

                Notification::make()
                    ->success()
                    ->title('Appointment confirmed')
                    ->body("Remember to contact {$record->patient_name} on {$record->phone}.")
                    ->send();
            });
    }

    /** Turn a request down. Hidden once the request already is rejected. */
    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Reject this appointment request?')
            ->modalDescription('The request stays in the list so you keep a record of it.')
            ->modalSubmitActionLabel('Yes, reject it')
            ->visible(fn (AppointmentRequest $record) => $record->status !== AppointmentStatus::Rejected)
            ->action(function (AppointmentRequest $record): void {
                $record->markAs(AppointmentStatus::Rejected);

                Notification::make()
                    ->warning()
                    ->title('Appointment request rejected')
                    ->send();
            });
    }

    /** Put a handled request back in the pending queue. */
    public static function reopen(): Action
    {
        return Action::make('reopen')
            ->label('Move back to pending')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (AppointmentRequest $record) => $record->status !== AppointmentStatus::Pending)
            ->action(function (AppointmentRequest $record): void {
                $record->update([
                    'status' => AppointmentStatus::Pending,
                    'responded_at' => null,
                ]);

                Notification::make()
                    ->info()
                    ->title('Moved back to pending')
                    ->send();
            });
    }
}
