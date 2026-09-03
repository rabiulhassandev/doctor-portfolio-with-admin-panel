<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Drag-and-drop reordering writes straight to `sort_order`, which is
            // the same column the public site orders by.
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                IconColumn::make('icon')
                    ->label('')
                    ->icon(fn (?string $state) => $state ?: 'heroicon-o-heart')
                    ->color('primary'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('short_description')
                    ->label('Description')
                    ->limit(60)
                    ->toggleable(),

                // Editable inline so staff can publish or hide a service without
                // opening the record.
                ToggleColumn::make('is_published')
                    ->label('Published'),

                ToggleColumn::make('is_featured')
                    ->label('On home page'),
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
            ->emptyStateHeading('No services yet')
            ->emptyStateDescription('Add the treatments and consultations you offer so patients can see them.');
    }
}
