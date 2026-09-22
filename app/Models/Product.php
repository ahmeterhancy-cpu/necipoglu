<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

class Product extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['name', 'summary', 'description'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'specs' => 'array',
            'price' => 'integer',
            'compare_price' => 'integer',
            'stock' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /* ── Stok ─────────────────────────────────────────────────────────── */

    public function isInStock(): bool
    {
        return ! $this->track_stock || $this->allow_backorder || $this->stock > 0;
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('track_stock', false)
            ->orWhere('allow_backorder', true)
            ->orWhere('stock', '>', 0));
    }

    /* ── Fiyat ────────────────────────────────────────────────────────── */

    /** Fiyat kuruş cinsinden saklanır; görüntülemede biçimlendirilir. */
    public function formattedPrice(?int $amount = null): string
    {
        return static::formatMoney($amount ?? $this->price, $this->currency);
    }

    public function formattedComparePrice(): ?string
    {
        return $this->compare_price ? static::formatMoney($this->compare_price, $this->currency) : null;
    }

    public function hasDiscount(): bool
    {
        return $this->compare_price !== null && $this->compare_price > $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((1 - $this->price / $this->compare_price) * 100);
    }

    public static function formatMoney(int $amount, string $currency = 'TRY'): string
    {
        $formatter = new NumberFormatter(app()->getLocale().'_CY', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $amount % 100 === 0 ? 0 : 2);

        return $formatter->formatCurrency($amount / 100, $currency);
    }

    /* ── Görseller ────────────────────────────────────────────────────── */

    public function coverImage(): ?string
    {
        return $this->images[0] ?? null;
    }
}
