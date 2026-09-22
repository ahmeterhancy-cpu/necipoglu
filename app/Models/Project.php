<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

/** Referans proje. */
class Project extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['title', 'summary', 'body', 'scope'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'year' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
