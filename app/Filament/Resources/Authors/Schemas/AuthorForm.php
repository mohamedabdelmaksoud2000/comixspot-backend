<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)->columnSpan(2),
                RichEditor::make('bio')
                    ->label('Bio')
                    ->nullable()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->maxLength(65535),
                FileUpload::make('avatar')
                    ->label('Avatar')
                    ->image()
                    ->disk('public')
                    ->nullable(),
            ]);
    }
}
