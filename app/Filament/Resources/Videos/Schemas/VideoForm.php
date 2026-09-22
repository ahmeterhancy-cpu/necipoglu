<?php

namespace App\Filament\Resources\Videos\Schemas;

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

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'title' => ['label' => 'Video başlığı', 'required' => true],
                        'description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rows' => 3],
                        'category' => ['label' => 'Kategori'],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    Select::make('provider')->label('Kaynak')->options(['youtube' => 'YouTube', 'vimeo' => 'Vimeo'])->default('youtube')->native(false),
                    TextInput::make('video_id')->label('Video kimliği')->helperText('YouTube adresindeki v= sonrası kısım. Örn: dQw4w9WgXcQ'),
                    FileUpload::make('poster')->label('Kapak görseli')->image()->directory('videos')->disk('public')->helperText('Boş bırakılırsa YouTube kapağı kullanılır.'),
                    TextInput::make('duration')->label('Süre (saniye)')->numeric(),
                    DatePicker::make('published_at')->label('Yayın tarihi'),
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    Toggle::make('is_featured')->label('Öne çıkan')->default(false),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
