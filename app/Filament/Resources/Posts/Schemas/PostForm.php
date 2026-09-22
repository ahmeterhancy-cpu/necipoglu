<?php

namespace App\Filament\Resources\Posts\Schemas;

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

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->schema([
                    Translatable::tabs([
                        'title' => ['label' => 'Başlık', 'required' => true],
                        'excerpt' => ['label' => 'Özet', 'type' => 'textarea', 'rows' => 3],
                        'body' => ['label' => 'İçerik', 'type' => 'textarea', 'rows' => 12],
                        'topic' => ['label' => 'Konu'],
                    ]),
                ]),

            Section::make('Ayarlar')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('URL adresi')->required()->unique(ignoreRecord: true),
                    TextInput::make('author')->label('Yazar'),
                    TextInput::make('read_minutes')->label('Okuma süresi (dk)')->numeric(),
                    FileUpload::make('cover')->label('Kapak görseli')->image()->directory('posts')->disk('public'),
                    DateTimePicker::make('published_at')->label('Yayın tarihi'),
                    Toggle::make('is_published')->label('Yayında')->default(false),
                ]),
        ]);
    }
}
