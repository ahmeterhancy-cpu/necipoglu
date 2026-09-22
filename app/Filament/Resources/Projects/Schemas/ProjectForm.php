<?php

namespace App\Filament\Resources\Projects\Schemas;

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

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'title' => ['label' => 'Proje adı', 'required' => true],
                        'summary' => ['label' => 'Özet', 'type' => 'textarea', 'rows' => 3],
                        'body' => ['label' => 'Detay', 'type' => 'textarea', 'rows' => 8],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    TextInput::make('client')->label('Müşteri'),
                    TextInput::make('location')->label('Konum'),
                    TextInput::make('year')->label('Yıl')->numeric(),
                    FileUpload::make('cover')->label('Kapak görseli')->image()->directory('projects')->disk('public'),
                    FileUpload::make('gallery')->label('Galeri')->image()->multiple()->reorderable()->directory('projects')->disk('public'),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    Toggle::make('is_featured')->label('Ana sayfada göster')->default(false),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
