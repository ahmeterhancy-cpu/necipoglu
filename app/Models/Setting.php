<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Anahtar/değer site ayarları. Okumalar tek sorguda önbelleğe alınır;
 * her yazmada önbellek temizlenir.
 */
class Setting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    protected const CACHE_KEY = 'settings.all';

    /** İstek içi bellek — aynı sayfada onlarca ayar okuması tek erişime iner. */
    protected static ?array $memo = null;

    protected static function booted(): void
    {
        $flush = function (): void {
            static::$memo = null;
            Cache::forget(static::CACHE_KEY);
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /** Tüm ayarlar anahtar => değer haritası olarak. */
    public static function map(): array
    {
        return static::$memo ??= Cache::rememberForever(
            static::CACHE_KEY,
            fn () => static::query()->pluck('value', 'key')->all(),
        );
    }

    /**
     * Tek bir ayarı okur. Değer çok dilliyse aktif dilin karşılığı döner.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::map()[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        if (is_array($value) && array_key_exists(app()->getLocale(), $value)) {
            return $value[app()->getLocale()] ?: ($value[config('site.default_locale')] ?? $default);
        }

        return $value;
    }

    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }
}
