<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal' => 'integer',
            'shipping_total' => 'integer',
            'discount_total' => 'integer',
            'grand_total' => 'integer',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /* ── Numara ───────────────────────────────────────────────────────── */

    /**
     * CN-2026-0001 biçiminde sıra numarası. Yıl içinde artar.
     * Çağıran taraf bunu transaction içinde kullanır; eşzamanlı iki
     * siparişte çakışma olursa unique kısıt yakalar ve yeniden denenir.
     */
    public static function nextNumber(): string
    {
        $year = now()->year;
        $prefix = "CN-{$year}-";

        $last = static::query()
            ->where('number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('number');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /* ── Görüntüleme ──────────────────────────────────────────────────── */

    public function formattedSubtotal(): string { return Money::format($this->subtotal, $this->currency); }
    public function formattedShipping(): string { return Money::format($this->shipping_total, $this->currency); }
    public function formattedTotal(): string { return Money::format($this->grand_total, $this->currency); }

    public function statusLabel(): string
    {
        return __('site.order.status.'.$this->status);
    }

    public function paymentLabel(): string
    {
        $label = config("commerce.payments.{$this->payment_method}.label");

        return $label[app()->getLocale()] ?? $label['tr'] ?? $this->payment_method;
    }

    public function shippingLabel(): string
    {
        $label = config("commerce.shipping.{$this->shipping_method}.label");

        return $label[app()->getLocale()] ?? $label['tr'] ?? $this->shipping_method;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }
}
