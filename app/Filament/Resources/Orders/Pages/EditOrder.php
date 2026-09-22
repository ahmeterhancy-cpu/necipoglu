<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return $this->record->number;
    }

    /** Sipariş silinmez — geçmiş kayıt olarak kalır, gerekirse iptal edilir. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /** Duruma göre zaman damgalarını doldur. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record;

        if ($data['status'] === 'confirmed' && ! $record->confirmed_at) {
            $data['confirmed_at'] = now();
        }

        if ($data['status'] === 'completed' && ! $record->completed_at) {
            $data['completed_at'] = now();
        }

        if ($data['status'] === 'cancelled' && ! $record->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        return $data;
    }

    /**
     * Panelden iptal edilen siparişin stoğu geri yüklenir — sipariş
     * verilirken düşülmüştü. Yalnızca iptale GEÇİŞTE bir kez çalışır,
     * zaten iptal olan bir kayıt tekrar kaydedilince stok şişmez.
     */
    protected function beforeSave(): void
    {
        $wasCancelled = $this->record->getOriginal('status') === 'cancelled';
        $willCancel = ($this->data['status'] ?? null) === 'cancelled';

        if ($wasCancelled || ! $willCancel) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->record->items as $item) {
                if ($item->product?->track_stock) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        });
    }
}
