<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

/** CN TV içeriği. */
class Video extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['title', 'description', 'category'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'duration' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Otomatik oynatma yok; kullanıcı tıklayınca yüklenir. */
    public function embedUrl(): ?string
    {
        return match ($this->provider) {
            'youtube' => $this->video_id ? "https://www.youtube-nocookie.com/embed/{$this->video_id}?rel=0&modestbranding=1&autoplay=1" : null,
            'vimeo' => $this->video_id ? "https://player.vimeo.com/video/{$this->video_id}?autoplay=1" : null,
            default => null,
        };
    }

    public function posterUrl(): ?string
    {
        if ($this->poster) {
            return null; // yerel dosya; görünümde storage yolu üretilir
        }

        return $this->provider === 'youtube' && $this->video_id
            ? "https://i.ytimg.com/vi/{$this->video_id}/maxresdefault.jpg"
            : null;
    }

    public function formattedDuration(): ?string
    {
        if (! $this->duration) {
            return null;
        }

        return sprintf('%d:%02d', intdiv($this->duration, 60), $this->duration % 60);
    }
}
