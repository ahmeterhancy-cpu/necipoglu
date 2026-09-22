<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SonMesajlar extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['md' => 12, 'xl' => 8];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Son mesajlar')
            ->query(ContactMessage::query()->latest()->limit(5))
            ->paginated(false)
            ->recordUrl(fn (ContactMessage $m) => ContactMessageResource::getUrl('edit', ['record' => $m]))
            ->headerActions([
                Action::make('tumu')
                    ->label('Tümü')
                    ->link()
                    ->url(ContactMessageResource::getUrl('index')),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Gönderen')
                    ->weight(fn (ContactMessage $m) => $m->read_at ? null : 'bold')
                    ->description(fn (ContactMessage $m) => $m->phone ?: $m->email),

                TextColumn::make('subject')
                    ->label('Konu')
                    ->placeholder('—')
                    ->limit(40),

                TextColumn::make('branch.name')
                    ->label('Şube')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->since()
                    ->dateTimeTooltip('d.m.Y H:i'),

                TextColumn::make('read_at')
                    ->label('')
                    ->badge()
                    ->state(fn (ContactMessage $m) => $m->read_at ? 'Okundu' : 'Yeni')
                    ->color(fn (ContactMessage $m) => $m->read_at ? 'gray' : 'warning'),
            ])
            ->emptyStateHeading('Henüz mesaj yok')
            ->emptyStateDescription('Sitedeki iletişim formundan gelen mesajlar burada görünür.')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
