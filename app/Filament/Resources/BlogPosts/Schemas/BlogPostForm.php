<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Forms\Components\PhotoUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            // Auto-fill the web address from the title, but only while
                            // creating — changing it later would break shared links.
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Web address')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefix(url('/blog').'/')
                            ->helperText('Avoid changing this after publishing — old links would stop working.'),

                        PhotoUpload::make('cover_image')
                            ->label('Cover image')
                            ->directory('blog')
                            ->guidance('Wide images work best, roughly 1200 x 630 pixels.'),

                        Textarea::make('excerpt')
                            ->label('Short summary')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Shown on the blog list and in search results. Leave empty to use the opening lines of the article.'),

                        RichEditor::make('content')
                            ->label('Article body')
                            ->required()
                            ->columnSpanFull()
                            // Images pasted into the editor land on the public disk
                            // alongside the other uploads.
                            ->fileAttachmentsDirectory('blog/inline')
                            ->fileAttachmentsVisibility('public'),
                    ]),

                Section::make('Publishing')
                    ->description('An article goes live only when it is switched on AND its publish date has passed.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Leave off to keep working on a draft.'),

                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->seconds(false)
                            ->default(now())
                            ->helperText('Set a future date to schedule the article.'),
                    ]),

                Section::make('Search engine listing (optional)')
                    ->description('Leave these blank to reuse the title and summary above.')
                    ->collapsed()
                    ->columns(1)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
            ]);
    }
}
