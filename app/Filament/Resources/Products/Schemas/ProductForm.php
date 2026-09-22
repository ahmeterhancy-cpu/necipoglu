<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Support\Translatable;
use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ürün bilgileri')
                ->schema([
                    Translatable::tabs([
                        'name' => ['label' => 'Ürün adı', 'required' => true],
                        'summary' => ['label' => 'Kısa açıklama', 'type' => 'textarea', 'rows' => 2],
                        'description' => ['label' => 'Detaylı açıklama', 'type' => 'textarea', 'rows' => 6],
                    ]),

                    TextInput::make('slug')
                        ->label('URL adresi')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Adres çubuğunda görünür. Yayına girdikten sonra değiştirmeyin.')
                        ->maxLength(255),
                ]),

            Section::make('Sınıflandırma')
                ->columns(2)
                ->schema([
                    Select::make('category_id')
                        ->label('Kategori')
                        ->options(fn () => Category::query()->ordered()->get()
                            ->mapWithKeys(fn ($c) => [$c->id => $c->name])->all())
                        ->searchable()
                        ->required()
                        ->native(false),

                    Select::make('brand_id')
                        ->label('Marka')
                        ->options(fn () => Brand::query()->ordered()->pluck('name', 'id')->all())
                        ->searchable()
                        ->native(false)
                        ->placeholder('Markasız'),

                    TextInput::make('sku')
                        ->label('Stok kodu')
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('unit')
                        ->label('Birim')
                        ->placeholder('m², adet, takım')
                        ->maxLength(255),
                ]),

            // Katalog modunda stok sitede hiç görünmüyor; alanlar gizlenir,
            // veritabanındaki değerlere dokunulmaz.
            Section::make(config('commerce.catalog') ? 'Fiyat' : 'Fiyat ve stok')
                ->columns(2)
                ->description('Fiyatlar KDV dahil girilir.')
                ->schema([
                    // Veritabanında kuruş tutulur; formda TL gösterilir.
                    TextInput::make('price')
                        ->label('Fiyat')
                        ->numeric()
                        ->required()
                        ->prefix('₺')
                        ->step('0.01')
                        ->formatStateUsing(fn (?int $state) => $state !== null ? $state / 100 : null)
                        ->dehydrateStateUsing(fn ($state) => (int) round(((float) $state) * 100)),

                    TextInput::make('compare_price')
                        ->label('Üstü çizili fiyat')
                        ->helperText('İndirim göstermek için. Boş bırakılabilir.')
                        ->numeric()
                        ->prefix('₺')
                        ->step('0.01')
                        ->formatStateUsing(fn (?int $state) => $state !== null ? $state / 100 : null)
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) round(((float) $state) * 100) : null),

                    TextInput::make('stock')
                        ->hidden(fn (): bool => (bool) config('commerce.catalog'))
                        ->label('Stok adedi')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Toggle::make('track_stock')
                        ->hidden(fn (): bool => (bool) config('commerce.catalog'))
                        ->label('Stok takibi yapılsın')
                        ->helperText('Kapatılırsa ürün her zaman satılabilir.')
                        ->default(true),

                    Toggle::make('allow_backorder')
                        ->hidden(fn (): bool => (bool) config('commerce.catalog'))
                        ->label('Stok bitse de sipariş alınsın')
                        ->default(false),
                ]),

            Section::make('Görseller')
                ->schema([
                    FileUpload::make('images')
                        ->label('Ürün görselleri')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->directory('products')
                        ->disk('public')
                        ->helperText('İlk görsel kapak olarak kullanılır. Sıralamayı sürükleyerek değiştirebilirsiniz.')
                        ->columnSpanFull(),
                ]),

            Section::make('Teknik özellikler')
                ->schema([
                    Repeater::make('specs')
                        ->hiddenLabel()
                        ->columns(2)
                        ->schema([
                            TextInput::make('label')->label('Özellik')->placeholder('Yüzey'),
                            TextInput::make('value')->label('Değer')->placeholder('Mat'),
                        ])
                        ->addActionLabel('Özellik ekle')
                        ->columnSpanFull(),
                ])
                ->collapsed(),

            Section::make('Yayın')
                ->columns(3)
                ->schema([
                    Toggle::make('is_active')->label('Yayında')->default(true),
                    Toggle::make('is_featured')->label('Ana sayfada öne çıkar')->default(false),
                    TextInput::make('position')->label('Sıra')->numeric()->default(0),
                ]),
        ]);
    }
}
