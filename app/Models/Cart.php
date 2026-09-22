<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** Sepetteki toplam parça adedi — üst bardaki rozet için. */
    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /** Ara toplam, kuruş. Fiyat her zaman güncel üründen okunur. */
    public function subtotal(): int
    {
        return (int) $this->items->sum(fn (CartItem $item) => $item->lineTotal());
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
}
