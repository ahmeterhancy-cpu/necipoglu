<?php

namespace App\Filament\Support;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Çok dilli alanlar JSON sütunda {"tr": …, "en": …} olarak durur.
 * Panelde her dil için ayrı sekme açılır; böylece form kalabalıklaşmaz ve
 * hangi dilin eksik olduğu görünür kalır.
 */
class Translatable
{
    /**
     * @param  array<string, array{type?: string, label: string, rows?: int, required?: bool}>  $fields
     *         anahtar = sütun adı
     */
    public static function tabs(array $fields): Tabs
    {
        $tabs = [];

        foreach (config('site.locales') as $code => $meta) {
            $components = [];

            foreach ($fields as $name => $options) {
                $components[] = static::field($name, $code, $options);
            }

            $tabs[] = Tab::make($meta['short'])->schema($components);
        }

        return Tabs::make('translations')->tabs($tabs)->columnSpanFull();
    }

    protected static function field(string $name, string $locale, array $options)
    {
        $path = "{$name}.{$locale}";
        $label = $options['label'];

        // Zorunluluk yalnızca varsayılan dilde aranır; ikinci dil sonradan
        // doldurulabilsin diye.
        $required = ($options['required'] ?? false) && $locale === config('site.default_locale');

        return match ($options['type'] ?? 'text') {
            'textarea' => Textarea::make($path)
                ->label($label)
                ->rows($options['rows'] ?? 3)
                ->required($required)
                ->columnSpanFull(),

            'rich' => RichEditor::make($path)
                ->label($label)
                ->required($required)
                ->columnSpanFull(),

            default => TextInput::make($path)
                ->label($label)
                ->required($required)
                ->maxLength(255),
        };
    }
}
