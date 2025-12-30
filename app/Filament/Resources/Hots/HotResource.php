<?php
namespace App\Filament\Resources\Hots;

use App\Filament\Resources\Hots\Pages\ListHots;
use App\Filament\Resources\Hots\Schemas\HotForm;
use App\Filament\Resources\Hots\Tables\HotsTable;
use App\Models\Donghua;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HotResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Donghuas';
    protected static ?int $navigationSort                      = 1;
    protected static ?string $model                            = Donghua::class;
    protected static ?string $recordTitleAttribute             = 'title_en';
    protected static ?string $modelLabel                       = 'Hot Donghua';
    protected static ?string $slug                             = 'hot-donghuas';
    protected static ?string $navigationBadgeColor             = 'danger';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('airing', true)
            ->orWhere('is_observed', true)
            ->count();
    }

    public static function form(Schema $schema): Schema
    {
        return HotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HotsTable::configure($table);
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
            'index' => ListHots::route('/'),
            // 'create' => CreateHot::route('/create'),
            // 'edit'   => EditHot::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query) {
                $query->where('airing', true)
                    ->orWhere('is_observed', true)
                    ->orderBy('title_en', 'asc');
            });
    }
}
