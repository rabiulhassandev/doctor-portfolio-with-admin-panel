<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AppointmentRequests\AppointmentRequestResource;
use App\Models\AppointmentRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * The queue: patients who have written in and not heard back.
 *
 * The dashboard used to end at the stat cards, which told staff *that* five
 * people were waiting but not who, so every morning started with a click
 * through to the full list. This puts the actual work on the front page, with
 * the oldest enquiry first — the person who has been waiting longest is the
 * one most likely to give up and ring somewhere else.
 */
class RecentAppointmentRequests extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AppointmentRequest::query()
                    ->pending()
                    // Oldest first: this is a queue, not a news feed.
                    ->orderBy('created_at')
            )
            ->heading('Waiting for a reply')
            ->description('The patients who have written in and not heard back yet.')
            ->emptyStateHeading('Nothing waiting')
            ->emptyStateDescription('Every enquiry has had an answer. Nice.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waiting')
                    ->since()
                    ->description(fn (AppointmentRequest $record) => $record->created_at->format('j M, g:i a'))
                    // Anything unanswered for two days is turning into a
                    // complaint, so it stops being grey and starts being red.
                    ->color(fn (AppointmentRequest $record) => $record->created_at->lt(now()->subDays(2)) ? 'danger' : null)
                    ->sortable(),

                TextColumn::make('patient_name')
                    ->label('Patient')
                    ->weight('bold')
                    ->description(fn (AppointmentRequest $record) => $record->phone)
                    ->searchable(),

                TextColumn::make('preferred_date')
                    ->label('Asked for')
                    ->date('D j M')
                    ->description(fn (AppointmentRequest $record) => $record->timeSlotLabel())
                    ->color(fn (AppointmentRequest $record) => $record->preferred_date->isPast() ? 'danger' : null)
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Note')
                    ->limit(60)
                    ->placeholder('No note left')
                    ->tooltip(fn (AppointmentRequest $record) => $record->message)
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-arrow-right')
                    ->url(fn (AppointmentRequest $record) => AppointmentRequestResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                Action::make('all')
                    ->label('See every request')
                    ->icon('heroicon-m-inbox-stack')
                    ->color('gray')
                    ->url(AppointmentRequestResource::getUrl()),
            ]);
    }

    /**
     * Only worth the vertical space once a request has actually arrived —
     * before that the empty state is just noise on a brand-new site.
     */
    public static function canView(): bool
    {
        return AppointmentRequest::query()->exists();
    }
}
