<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['name', 'kind', 'address'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'phones' => 'array',
            'hours' => 'array',
            'lat' => 'float',
            'lng' => 'float',
            'position' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Telefonun tel: bağlantısı için sadeleştirilmiş hali. */
    public function dialable(string $phone): string
    {
        return preg_replace('/[^\d+]/', '', $phone);
    }

    public function primaryPhone(): ?string
    {
        return $this->phones[0] ?? null;
    }

    /**
     * Anahtar gerektirmeyen Google Maps gömme adresi.
     * Koordinat girilmişse onu, girilmemişse adres metnini kullanır —
     * böylece panelde koordinat olmadan da harita çalışır.
     */
    public function mapEmbedUrl(): string
    {
        $query = $this->lat !== null && $this->lng !== null
            ? "{$this->lat},{$this->lng}"
            : $this->addressQuery();

        return 'https://www.google.com/maps?'.http_build_query([
            'q' => $query,
            'hl' => app()->getLocale(),
            'z' => 16,
            'output' => 'embed',
        ]);
    }

    public function directionsUrl(): string
    {
        if ($this->map_url) {
            return $this->map_url;
        }

        $destination = $this->lat !== null && $this->lng !== null
            ? "{$this->lat},{$this->lng}"
            : $this->addressQuery();

        return 'https://www.google.com/maps/dir/?'.http_build_query([
            'api' => 1,
            'destination' => $destination,
        ]);
    }

    protected function addressQuery(): string
    {
        return trim(collect([$this->address, $this->city, 'Kuzey Kıbrıs'])->filter()->implode(', '));
    }
}
