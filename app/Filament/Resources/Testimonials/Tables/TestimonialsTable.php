<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn () => null),

                TextColumn::make('patient_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->patient_title),

                TextColumn::make('message')
                    ->label('Testimonial')
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (?int $state) => $state ? str_repeat('★', $state) : 'No rating')
                    ->badge()
                    ->color(fn (?int $state) => $state >= 4 ? 'success' : 'gray'),

                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All testimonials')
                    ->trueLabel('Published only')
                    ->falseLabel('Hidden only'),
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
            ->emptyStateHeading('No testimonials yet')
            ->emptyStateDescription('Add a few patient quotes to build trust with new visitors.');
    }
}
