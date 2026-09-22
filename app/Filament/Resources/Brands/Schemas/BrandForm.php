<?php

namespace App\Filament\Resources\Brands\Schemas;

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

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rows' => 3],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Marka adı')->required(),
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    TextInput::make('website')->label('Web sitesi')->url()->helperText('Marka sayfasındaki "Marka sitesi" düğmesi buraya gider. Boşsa düğme hiç görünmez.'),
                    FileUpload::make('logo')->label('Logo')->image()->directory('brands')->disk('public')->helperText('Tercihen SVG ya da saydam PNG.'),

                    FileUpload::make('images')
                        ->label('Marka sayfası görselleri')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->directory('brands/gallery')
                        ->disk('public')
                        ->columnSpanFull()
                        ->helperText('Marka sayfasındaki şeritte bu sırayla görünür. Boş bırakılırsa public/brand/gallery/<url-adresi>/ klasöründeki dosyalar kullanılır.'),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    Toggle::make('is_featured')->label('Öne çıkan')->default(false),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
