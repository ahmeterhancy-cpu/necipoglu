<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Support\Money;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Sipariş ekranı ağırlıkla OKUMA amaçlıdır: tutarlar, kalemler ve müşteri
 * bilgileri sipariş anındaki haliyle dondurulmuştur, panelden değiştirilmez.
 * Yalnızca durum ve ödeme durumu düzenlenebilir.
 */
class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Durum')
                ->description('Siparişin işleyişini buradan yönetirsiniz.')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('Sipariş durumu')
                        ->options(fn () => collect([
                            'pending', 'confirmed', 'preparing', 'ready', 'shipped', 'completed', 'cancelled',
                        ])->mapWithKeys(fn ($s) => [$s => __('site.order.status.'.$s)])->all())
                        ->required()
                        ->native(false),

                    Select::make('payment_status')
                        ->label('Ödeme durumu')
                        ->options([
                            'unpaid' => 'Bekliyor',
                            'paid' => 'Ödendi',
                            'refunded' => 'İade edildi',
                        ])
                        ->required()
                        ->native(false),
                ]),

            Section::make('Müşteri')
                ->columns(3)
                ->schema([
                    TextEntry::make('customer_name')->label('Ad soyad'),
                    TextEntry::make('customer_phone')->label('Telefon')->copyable(),
                    TextEntry::make('customer_email')->label('E-posta')->copyable()->placeholder('—'),

                    TextEntry::make('shipping_method')
                        ->label('Teslimat')
                        ->state(fn (Model $record) => $record->shippingLabel()
                            .($record->branch ? ' — '.$record->branch->name : '')),

                    TextEntry::make('payment_method')
                        ->label('Ödeme yöntemi')
                        ->state(fn (Model $record) => $record->paymentLabel()),

                    TextEntry::make('created_at')->label('Sipariş tarihi')->dateTime('d.m.Y H:i'),

                    TextEntry::make('shipping_address')
                        ->label('Teslimat adresi')
                        ->columnSpanFull()
                        ->placeholder('Şubeden teslim alınacak')
                        ->state(fn (Model $record) => $record->shipping_address
                            ? collect([
                                $record->shipping_address['line'] ?? null,
                                $record->shipping_address['district'] ?? null,
                                $record->shipping_address['city'] ?? null,
                            ])->filter()->implode(', ')
                            : null),

                    TextEntry::make('note')
                        ->label('Müşteri notu')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Kalemler')
                ->schema([
                    TextEntry::make('items')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->listWithLineBreaks()
                        ->state(fn (Model $record) => $record->items
                            ->map(fn ($item) => "{$item->name}  ×{$item->quantity}  ·  ".$item->formattedLineTotal())
                            ->all()),

                    Grid::make(3)->schema([
                        TextEntry::make('subtotal')
                            ->label('Ara toplam')
                            ->state(fn (Model $record) => Money::format($record->subtotal, $record->currency)),

                        TextEntry::make('shipping_total')
                            ->label('Teslimat')
                            ->state(fn (Model $record) => $record->shipping_total === 0
                                ? 'Ücretsiz'
                                : Money::format($record->shipping_total, $record->currency)),

                        TextEntry::make('grand_total')
                            ->label('Genel toplam')
                            ->weight('bold')
                            ->size('lg')
                            ->state(fn (Model $record) => Money::format($record->grand_total, $record->currency)),
                    ]),
                ]),
        ]);
    }
}
