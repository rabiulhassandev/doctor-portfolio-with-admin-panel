<?php

namespace App\Filament\Resources\AppointmentRequests\Tables;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentRequests\Actions\AppointmentStatusActions;
use App\Models\AppointmentRequest;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Newest enquiries first: the top of the list is the work to do today.
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('j M, g:i a')
                    ->description(fn (AppointmentRequest $record) => $record->created_at->diffForHumans())
                    ->sortable(),

                TextColumn::make('patient_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Phone and email sit in one column, each click-to-contact, so
                // staff can reach the patient straight from the list.
                TextColumn::make('phone')
                    ->label('Contact')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->icon('heroicon-m-phone')
                    ->url(fn (AppointmentRequest $record) => 'tel:'.$record->phone)
                    ->description(fn (AppointmentRequest $record) => $record->email ?: 'No email given'),

                TextColumn::make('preferred_date')
                    ->label('Requested for')
                    ->date('D j M Y')
                    ->sortable()
                    ->description(fn (AppointmentRequest $record) => $record->timeSlotLabel())
                    ->color(fn (AppointmentRequest $record) => $record->preferred_date->isPast() ? 'danger' : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AppointmentStatus::class)
                    ->label('Status')
                    ->placeholder('All requests'),

                Filter::make('upcoming')
                    ->label('Requested date still to come')
                    ->query(fn (Builder $query) => $query->whereDate('preferred_date', '>=', today())),
            ])
            ->recordActions([
                AppointmentStatusActions::confirm(),
                AppointmentStatusActions::reject(),
                ActionGroup::make([
                    ViewAction::make(),
                    AppointmentStatusActions::reopen(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No appointment requests yet')
            ->emptyStateDescription('Requests sent through the website contact form will appear here.');
    }
}
