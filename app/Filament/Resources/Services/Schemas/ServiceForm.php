<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    /**
     * Icons offered in the dropdown. Every one of these ships with Filament, so
     * the doctor picks a picture instead of typing a class name. To add more,
     * copy an "outline" name from heroicons.com, e.g. heroicon-o-beaker.
     *
     * @var array<string, string>
     */
    public const ICONS = [
        'heroicon-o-heart' => 'Heart',
        'heroicon-o-beaker' => 'Laboratory',
        'heroicon-o-clipboard-document-check' => 'Check-up',
        'heroicon-o-shield-check' => 'Prevention',
        'heroicon-o-bolt' => 'Emergency',
        'heroicon-o-user-group' => 'Family care',
        'heroicon-o-academic-cap' => 'Consultation',
        'heroicon-o-chart-bar' => 'Diagnostics',
        'heroicon-o-sparkles' => 'Wellness',
        'heroicon-o-clock' => 'Follow-up',
        'heroicon-o-video-camera' => 'Telemedicine',
        'heroicon-o-home-modern' => 'Home visit',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service details')
                    ->description('This is what patients see on the Services page.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            // Keep the URL slug in step with the title while typing,
                            // but only on create so existing links never break.
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the page link. Letters, numbers and dashes only.'),

                        Select::make('icon')
                            ->label('Icon')
                            ->options(self::ICONS)
                            ->default('heroicon-o-heart')
                            ->required()
                            ->native(false)
                            ->helperText('Shown in the coloured circle above the service name.'),

                        TextInput::make('sort_order')
                            ->label('Display order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first.'),

                        Textarea::make('short_description')
                            ->label('Short description')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('One or two sentences. Appears on the service card.'),

                        Textarea::make('description')
                            ->label('Full description (optional)')
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Extra detail shown underneath the short description.'),
                    ]),

                Section::make('Visibility')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Turn off to hide this service from the website.'),

                        Toggle::make('is_featured')
                            ->label('Feature on the home page')
                            ->helperText('Featured services appear in the home-page preview.'),
                    ]),
            ]);
    }
}
