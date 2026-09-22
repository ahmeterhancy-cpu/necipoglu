<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['sector', 'tagline', 'description'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'founded' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
