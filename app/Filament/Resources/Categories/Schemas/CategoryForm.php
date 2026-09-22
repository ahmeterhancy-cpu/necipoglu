<?php

namespace App\Filament\Resources\Categories\Schemas;

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

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'name' => ['label' => 'Kategori adı', 'required' => true],
                        'tagline' => ['label' => 'Kısa tanım'],
                        'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rows' => 4],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    FileUpload::make('thumbnail')->label('Kart görseli')->image()->directory('categories')->disk('public')->helperText('Ana sayfadaki kartta kullanılır. Dikey (3:4) görsel önerilir.'),
                    FileUpload::make('cover')->label('Kapak görseli')->image()->directory('categories')->disk('public'),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    Toggle::make('show_on_home')->label('Ana sayfada göster')->default(true),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
