<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'line_total' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Ürün sonradan silinmiş olabilir; ad ve fiyat siparişte saklı. */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function formattedUnitPrice(): string
    {
        return Money::format($this->unit_price, $this->order?->currency);
    }

    public function formattedLineTotal(): string
    {
        return Money::format($this->line_total, $this->order?->currency);
    }
}
