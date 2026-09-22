<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    /** Sipariş mağazadan gelir; panelden elle oluşturulmaz. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /** Günlük iş akışına göre sekmeler: önce ilgilenilmesi gerekenler. */
    public function getTabs(): array
    {
        $model = static::getResource()::getModel();

        return [
            'open' => Tab::make('Açık')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('status', ['completed', 'cancelled']))
                ->badge(fn () => $model::whereNotIn('status', ['completed', 'cancelled'])->count()),

            'unpaid' => Tab::make('Ödeme bekleyen')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('payment_status', 'unpaid')
                    ->where('status', '!=', 'cancelled'))
                ->badge(fn () => $model::where('payment_status', 'unpaid')
                    ->where('status', '!=', 'cancelled')->count()),

            'completed' => Tab::make('Tamamlanan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

            'cancelled' => Tab::make('İptal')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),

            'all' => Tab::make('Tümü'),
        ];
    }
}
