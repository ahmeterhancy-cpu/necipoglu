<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

/**
 * Çok dilli alanlar için hafif çeviri desteği — harici paket yok.
 *
 * Modelde çevrilebilir alanları tanımla:
 *
 *     protected array $translatable = ['name', 'description'];
 *
 * Sütun JSON tutar: {"tr": "Duşakabin", "en": "Shower enclosure"}
 * Okurken aktif dil, yoksa varsayılan dil, o da yoksa ilk dolu değer döner.
 * Yazarken düz dize verilirse aktif dile yazılır; dizi verilirse olduğu gibi.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable ?? [] as $attribute) {
            $this->casts[$attribute] = 'array';
        }
    }

    public function isTranslatable(string $key): bool
    {
        return in_array($key, $this->translatable ?? [], true);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (! $this->isTranslatable($key) || ! is_array($value)) {
            return $value;
        }

        return $this->pickTranslation($value);
    }

    public function setAttribute($key, $value)
    {
        if ($this->isTranslatable($key) && ! is_array($value) && ! is_null($value)) {
            $existing = $this->getTranslations($key);
            $existing[App::getLocale()] = $value;
            $value = $existing;
        }

        return parent::setAttribute($key, $value);
    }

    /** Ham çeviri dizisi — yönetim paneli ve dışa aktarım için. */
    public function getTranslations(string $key): array
    {
        $value = $this->attributes[$key] ?? null;

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /** Tek bir dildeki değer — hreflang alternatifleri ve panel için. */
    public function translate(string $key, string $locale): ?string
    {
        return $this->getTranslations($key)[$locale] ?? null;
    }

    protected function pickTranslation(array $value): mixed
    {
        foreach ([App::getLocale(), config('site.default_locale')] as $locale) {
            if (filled($value[$locale] ?? null)) {
                return $value[$locale];
            }
        }

        foreach ($value as $candidate) {
            if (filled($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
