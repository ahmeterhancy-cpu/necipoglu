<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Satır tutarı, kuruş. Fiyat sepete eklendiği andaki değil GÜNCEL
     * üründen okunur — müşteri ödeme ekranında gördüğü fiyatı öder.
     */
    public function lineTotal(): int
    {
        return (int) ($this->product?->price ?? 0) * $this->quantity;
    }
}
