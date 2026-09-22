<?php

namespace App\Filament\Resources\Branches\Schemas;

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

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'name' => ['label' => 'Şube adı', 'required' => true],
                        'kind' => ['label' => 'Tür', 'required' => false],
                        'address' => ['label' => 'Adres', 'type' => 'textarea', 'rows' => 3, 'required' => true],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    TextInput::make('city')->label('Şehir'),
                    TagsInput::make('phones')->label('Telefonlar')->placeholder('+90 542 000 0000'),
                    TextInput::make('whatsapp')->label('WhatsApp numarası'),
                    TextInput::make('email')->label('E-posta')->email(),
                    TextInput::make('lat')->label('Enlem')->numeric()->helperText('Boş bırakılırsa harita adres metnine göre çalışır.'),
                    TextInput::make('lng')->label('Boylam')->numeric(),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
