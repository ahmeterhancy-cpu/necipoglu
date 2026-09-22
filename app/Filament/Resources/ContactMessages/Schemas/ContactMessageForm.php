<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject'),
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('locale')
                    ->required()
                    ->default('tr'),
                TextInput::make('source'),
                TextInput::make('ip'),
                DateTimePicker::make('read_at'),
            ]);
    }
}
