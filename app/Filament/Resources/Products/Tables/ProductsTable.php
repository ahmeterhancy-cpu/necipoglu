<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Brand;
use App\Models\Category;
use App\Support\Money;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                ImageColumn::make('images')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->limit(1),

                TextColumn::make('name')
                    ->label('Ürün')
                    ->description(fn (Model $record) => $record->sku)
                    ->searchable(query: fn ($query, string $search) => $query->where('name', 'like', "%{$search}%"))
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('brand.name')
                    ->label('Marka')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->formatStateUsing(fn (int $state, Model $record) => Money::format($state, $record->currency))
                    ->description(fn (Model $record) => $record->unit ? '/ '.$record->unit : null)
                    ->sortable()
                    ->alignEnd(),

                // Stok tükendiğinde ya da azaldığında listede hemen görünsün.
                TextColumn::make('stock')
                    ->hidden(fn (): bool => (bool) config('commerce.catalog'))
                    ->label('Stok')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (Model $record) => match (true) {
                        ! $record->track_stock => 'gray',
                        $record->stock <= 0 => 'danger',
                        $record->stock < 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (int $state, Model $record) => $record->track_stock ? $state : '∞'),

                IconColumn::make('is_active')->label('Yayında')->boolean(),
                IconColumn::make('is_featured')->label('Öne çıkan')->boolean()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn () => Category::query()->ordered()->get()
                        ->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()),

                SelectFilter::make('brand_id')
                    ->label('Marka')
                    ->options(fn () => Brand::query()->ordered()->pluck('name', 'id')->all()),

                TernaryFilter::make('is_active')->label('Yayın durumu'),

                TernaryFilter::make('stock')
                    ->hidden(fn (): bool => (bool) config('commerce.catalog'))
                    ->label('Stokta')
                    ->queries(
                        true: fn ($query) => $query->where('stock', '>', 0),
                        false: fn ($query) => $query->where('stock', '<=', 0),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
