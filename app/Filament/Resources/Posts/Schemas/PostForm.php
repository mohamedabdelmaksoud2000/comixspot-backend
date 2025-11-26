<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                ->description('Manage the details of the blog post.')
                ->schema([
                    TextInput::make('title')
                    ->required()
                    ->reactive()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, $state,$record){
                        if (!$record) {
                            $slug = Str::slug($state);

                            $count = \App\Models\Post::where('slug', 'like', "{$slug}%")->count();
                            if ($count) {
                                $slug .= '-' . ($count + 1);
                            }

                            $set('slug', $slug);
                        }  
                    })
                    ->maxLength(255)
                    ->columnSpan(1),
                    
                    TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->columnSpan(1),

                    FileUpload::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->image()
                    ->required()
                    ->maxSize('2048') // 2M
                    ->columnSpan(2),

                    RichEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->maxLength(50000)
                    ->minLength(1000)
                    ->columnSpan(2),
                ])
                ->collapsible()
                ->columns(2)
                ->columnSpan(2),
                
                Section::make('Additional Information')
                ->description('Manage additional information for the blog post.')
                ->schema([
                    TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(200)
                    ->minLength(50)
                    ->required(),

                    TextInput::make('meta_description')
                    ->label('Meta Description')
                    ->maxLength(300)
                    ->minLength(100)
                    ->required(),
                    Select::make('author_id')
                    ->label('Author')
                    ->relationship(name:'author',titleAttribute:'name')
                    ->searchable()
                    ->loadingMessage('Loading authors...')
                    ->required(),
                    TextInput::make('time_read')
                    ->label('Time to Read (minutes)')
                    ->numeric()
                    ->integer()
                    ->required(),

                    \Filament\Forms\Components\DateTimePicker::make('published_at')
                    ->label('Published At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->nullable(),

                ])
                ->collapsible()
                ->columnSpan(1),
                
            ])->columns(3);
    }
}
