<?php

namespace App\Filament\Resources\GalleryImages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GalleryImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('Photo')
                    ->height(56)
                    ->width(84),

                TextColumn::make('caption')
                    ->searchable()
                    ->placeholder('No caption')
                    ->weight('medium'),

                TextColumn::make('alt_text')
                    ->label('Image description')
                    ->placeholder('Falls back to the caption')
                    ->limit(50)
                    ->toggleable(),

                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No photos yet')
            ->emptyStateDescription('Upload photos of the clinic so patients know what to expect.');
    }
}
