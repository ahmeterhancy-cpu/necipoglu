<?php

namespace App\Filament\Resources\Subsidiaries\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubsidiaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'sector' => ['label' => 'Sektör'],
                        'tagline' => ['label' => 'Kısa tanım'],
                        'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rows' => 4],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Şirket adı')->required(),
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    TextInput::make('website')->label('Web sitesi')->url(),
                    TextInput::make('founded')->label('Kuruluş yılı')->numeric(),
                    FileUpload::make('logo')->label('Logo')->image()->directory('subsidiaries')->disk('public'),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
