<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Support\Money;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('Sipariş no')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Müşteri')
                    ->description(fn (Model $record) => $record->customer_phone)
                    ->searchable(['customer_name', 'customer_phone', 'customer_email']),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __('site.order.status.'.$state))
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed', 'preparing' => 'info',
                        'ready', 'shipped' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Ödeme')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'paid' => 'Ödendi',
                        'refunded' => 'İade edildi',
                        default => 'Bekliyor',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->label('Yöntem')
                    ->formatStateUsing(fn (Model $record) => $record->paymentLabel())
                    ->toggleable(),

                TextColumn::make('shipping_method')
                    ->label('Teslimat')
                    ->formatStateUsing(fn (Model $record) => $record->shippingLabel())
                    ->description(fn (Model $record) => $record->branch?->name)
                    ->toggleable(),

                // Tutar kuruş tutuluyor; listede biçimlendirilerek gösterilir.
                TextColumn::make('grand_total')
                    ->label('Toplam')
                    ->formatStateUsing(fn (int $state, Model $record) => Money::format($state, $record->currency))
                    ->sortable()
                    ->alignEnd()
                    ->weight('medium'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(fn () => collect([
                        'pending', 'confirmed', 'preparing', 'ready', 'shipped', 'completed', 'cancelled',
                    ])->mapWithKeys(fn ($s) => [$s => __('site.order.status.'.$s)])->all()),

                SelectFilter::make('payment_status')
                    ->label('Ödeme durumu')
                    ->options([
                        'unpaid' => 'Bekliyor',
                        'paid' => 'Ödendi',
                        'refunded' => 'İade edildi',
                    ]),

                SelectFilter::make('shipping_method')
                    ->label('Teslimat')
                    ->options(fn () => collect(config('commerce.shipping'))
                        ->mapWithKeys(fn ($m, $k) => [$k => $m['label']['tr']])->all()),
            ])
            ->recordActions([
                // En sık yapılan iki işlem listeden tek tıkla yapılabilsin.
                Action::make('markPaid')
                    ->label('Ödendi işaretle')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->color('success')
                    ->visible(fn (Model $record) => $record->payment_status !== 'paid' && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->action(fn (Model $record) => $record->update([
                        'payment_status' => 'paid',
                        'status' => $record->status === 'pending' ? 'confirmed' : $record->status,
                        'confirmed_at' => $record->confirmed_at ?? now(),
                    ])),

                EditAction::make()->label('Aç'),
            ]);
    }
}
