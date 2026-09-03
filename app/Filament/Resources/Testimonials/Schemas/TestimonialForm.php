<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Forms\Components\PhotoUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Patient')
                    ->columns(2)
                    ->schema([
                        TextInput::make('patient_name')
                            ->label('Patient name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('patient_title')
                            ->label('Caption under the name (optional)')
                            ->maxLength(255)
                            ->placeholder('Cardiac patient, 2024'),

                        PhotoUpload::make('photo')
                            ->label('Photo (optional)')
                            // Crops to a circle, matching how the quote is shown
                            // on the home page.
                            ->avatar()
                            ->directory('testimonials')
                            ->maxSize(2048)
                            ->guidance('Square photos look best; leave empty to show the patient initials instead.'),

                        Select::make('rating')
                            ->label('Star rating (optional)')
                            ->options([
                                5 => '5 stars',
                                4 => '4 stars',
                                3 => '3 stars',
                                2 => '2 stars',
                                1 => '1 star',
                            ])
                            ->default(5)
                            ->native(false)
                            ->helperText('Leave empty to hide the stars for this quote.'),
                    ]),

                Section::make('Their words')
                    ->schema([
                        Textarea::make('message')
                            ->label('Testimonial')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Always get the patient permission before publishing their words.'),
                    ]),

                Section::make('Visibility')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Turn off to hide this testimonial from the website.'),

                        TextInput::make('sort_order')
                            ->label('Display order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first.'),
                    ]),
            ]);
    }
}
