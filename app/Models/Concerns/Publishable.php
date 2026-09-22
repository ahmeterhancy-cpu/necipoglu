<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/** is_active + position taşıyan içerik modelleri için ortak sorgu kapsamları. */
trait Publishable
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
