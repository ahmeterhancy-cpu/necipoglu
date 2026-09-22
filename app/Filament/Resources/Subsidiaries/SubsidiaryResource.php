<?php

namespace App\Filament\Resources\Subsidiaries;

use App\Filament\Resources\Subsidiaries\Pages\CreateSubsidiary;
use App\Filament\Resources\Subsidiaries\Pages\EditSubsidiary;
use App\Filament\Resources\Subsidiaries\Pages\ListSubsidiaries;
use App\Filament\Resources\Subsidiaries\Schemas\SubsidiaryForm;
use App\Filament\Resources\Subsidiaries\Tables\SubsidiariesTable;
use App\Models\Subsidiary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubsidiaryResource extends Resource
{
    protected static ?string $model = Subsidiary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'İştirakler';

    protected static ?string $modelLabel = 'iştirak';

    protected static ?string $pluralModelLabel = 'iştirakler';

    protected static string|\UnitEnum|null $navigationGroup = 'Kurumsal';

    protected static ?int $navigationSort = 2;

    /**
     * İştirakler sitenin menüsünden kaldırıldı (yerine Dura Coffee geldi);
     * panelde de gösterilmiyor. Model ve tablo yerinde duruyor, geri açmak
     * için bu metodu silmek yeterli.
     */
    public static function canAccess(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return SubsidiaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubsidiariesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubsidiaries::route('/'),
            'create' => CreateSubsidiary::route('/create'),
            'edit' => EditSubsidiary::route('/{record}/edit'),
        ];
    }
}
