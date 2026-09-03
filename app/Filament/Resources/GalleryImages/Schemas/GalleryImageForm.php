<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use App\Filament\Forms\Components\PhotoUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Photo')
                    ->columns(2)
                    ->schema([
                        PhotoUpload::make('image')
                            ->label('Image')
                            ->required()
                            ->directory('gallery')
                            ->columnSpanFull()
                            ->guidance('Landscape photos fill the grid most neatly.'),

                        TextInput::make('caption')
                            ->label('Caption (optional)')
                            ->maxLength(255)
                            ->helperText('Shown underneath the photo when it is opened.'),

                        TextInput::make('alt_text')
                            ->label('Image description (optional)')
                            ->maxLength(255)
                            ->helperText('Read aloud by screen readers. Falls back to the caption if left empty.'),
                    ]),

                Section::make('Visibility')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Turn off to hide this photo from the website.'),

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
