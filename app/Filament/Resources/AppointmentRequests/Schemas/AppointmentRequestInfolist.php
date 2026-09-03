<?php

namespace App\Filament\Resources\AppointmentRequests\Schemas;

use App\Models\AppointmentRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

/** Read-only detail view of one enquiry. */
class AppointmentRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Patient contact')
                    ->description('Reach out to the patient to agree the final time.')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('patient_name')
                            ->label('Name')
                            ->weight('bold')
                            ->size(TextSize::Large),

                        TextEntry::make('phone')
                            ->label('Phone')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->copyMessage('Phone number copied')
                            ->url(fn (AppointmentRequest $record) => 'tel:'.$record->phone),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copied')
                            ->placeholder('Not provided')
                            ->url(fn (AppointmentRequest $record) => $record->email ? 'mailto:'.$record->email : null),
                    ]),

                Section::make('What they asked for')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('preferred_date')
                            ->label('Preferred date')
                            ->date('l j F Y'),

                        TextEntry::make('preferred_time')
                            ->label('Preferred time')
                            ->state(fn (AppointmentRequest $record) => $record->timeSlotLabel()),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),

                        TextEntry::make('message')
                            ->label('Their message')
                            ->placeholder('No message left')
                            ->columnSpanFull(),
                    ]),

                Section::make('Internal record')
                    ->description('Only staff see this section.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Request received')
                            ->dateTime('j F Y, g:i a'),

                        TextEntry::make('responded_at')
                            ->label('Answered on')
                            ->dateTime('j F Y, g:i a')
                            ->placeholder('Not answered yet'),

                        TextEntry::make('admin_notes')
                            ->label('Staff notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
