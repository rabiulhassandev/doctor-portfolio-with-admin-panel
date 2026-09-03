<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->height(40)
                    ->width(64),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(50)
                    ->description(fn ($record) => '/blog/'.$record->slug),

                TextColumn::make('published_at')
                    ->label('Publish date')
                    ->dateTime('j M Y, g:i a')
                    ->sortable()
                    ->placeholder('Not scheduled'),

                // The toggle alone does not tell the whole story — a post can be
                // switched on but still scheduled — so spell out the live state.
                TextColumn::make('status')
                    ->label('Visible on site')
                    ->badge()
                    ->state(function ($record): string {
                        if (! $record->is_published) {
                            return 'Draft';
                        }

                        if ($record->published_at === null) {
                            return 'No date set';
                        }

                        return $record->published_at->isFuture() ? 'Scheduled' : 'Live';
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Live' => 'success',
                        'Scheduled' => 'info',
                        default => 'gray',
                    }),

                ToggleColumn::make('is_published')
                    ->label('Published'),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All articles')
                    ->trueLabel('Published only')
                    ->falseLabel('Drafts only'),
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
            ->emptyStateHeading('No articles yet')
            ->emptyStateDescription('Write your first health article to give patients something to read.');
    }
}
