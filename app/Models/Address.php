<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Siparişe kopyalanacak anlık görüntü. */
    public function snapshot(): array
    {
        return [
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'city' => $this->city,
            'district' => $this->district,
            'line' => $this->line,
        ];
    }

    public function oneLine(): string
    {
        return collect([$this->line, $this->district, $this->city])->filter()->implode(', ');
    }
}
