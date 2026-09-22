<?php

namespace App\Filament\Resources\Admins;

use App\Filament\Resources\Admins\Pages\CreateAdmin;
use App\Filament\Resources\Admins\Pages\EditAdmin;
use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Panele girebilen hesaplar. Yöneticiler `users` tablosunda `is_admin`
 * işaretiyle durur; burada yalnızca onlar listelenir.
 *
 * İlk hesap sunucuda `admin:olustur` ile açılır (SSH olmadığı için başka
 * yol yok); sonrakilerin hepsi bu ekrandan eklenir.
 */
class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Yöneticiler';

    protected static ?string $modelLabel = 'yönetici';

    protected static ?string $pluralModelLabel = 'yöneticiler';

    protected static string|\UnitEnum|null $navigationGroup = 'Kurumsal';

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'yoneticiler';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_admin', true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hesap')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Ad soyad')
                        ->required()
                        ->maxLength(120),

                    TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->maxLength(160)
                        ->unique(ignoreRecord: true),
                ]),

            Section::make('Şifre')
                ->columns(2)
                ->description(fn (string $operation) => $operation === 'edit'
                    ? 'Değiştirmeyecekseniz boş bırakın.'
                    : 'En az 10 karakter.')
                ->schema([
                    // Model "hashed" cast'i şifreyi kendisi özetler; burada
                    // yalnızca doluysa gönderilir ki düzenlemede boş alan
                    // mevcut şifreyi silmesin.
                    TextInput::make('password')
                        ->label('Şifre')
                        ->password()
                        ->revealable()
                        ->minLength(10)
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->same('password_confirmation')
                        ->autocomplete('new-password'),

                    TextInput::make('password_confirmation')
                        ->label('Şifre (tekrar)')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(false)
                        ->autocomplete('new-password'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad soyad')
                    ->searchable()
                    ->description(fn (User $record) => $record->is(auth()->user()) ? 'Siz' : null),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Eklenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at')
            ->recordActions([
                EditAction::make(),
                static::deleteAction(),
            ]);
    }

    /**
     * Kendi hesabınızı ve son kalan yöneticiyi silmek kapalı — panel
     * sahipsiz kalmasın.
     */
    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->hidden(fn (User $record) => ! static::canBeRemoved($record));
    }

    public static function canBeRemoved(User $record): bool
    {
        return ! $record->is(auth()->user())
            && User::where('is_admin', true)->count() > 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
            'create' => CreateAdmin::route('/create'),
            'edit' => EditAdmin::route('/{record}/edit'),
        ];
    }
}
