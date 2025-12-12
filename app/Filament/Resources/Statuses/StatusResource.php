<?php
namespace App\Filament\Resources\Statuses;

use App\Filament\Resources\Statuses\Pages\CreateStatus;
use App\Filament\Resources\Statuses\Pages\EditStatus;
use App\Filament\Resources\Statuses\Pages\ListStatuses;
use App\Models\Status;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;


class StatusResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $model = Status::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('slug')->required()->unique(),
                Forms\Components\ColorPicker::make('text_color')->required(),
                Forms\Components\ColorPicker::make('bg_color')->required(),
                Forms\Components\Toggle::make('is_active')->required()->default(true),
                Forms\Components\TextInput::make('order')->default(0),
            ])
        ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->sortable()->alignCenter()->toggleable(isToggledHiddenByDefault: true)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->sortable()->searchable()->badge(),
                Tables\Columns\ColorColumn::make('text_color')->alignCenter(),
                Tables\Columns\ColorColumn::make('bg_color')->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->alignCenter()->boolean()
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->delete())
            ])
            ->defaultSort('order', 'asc')
            ->recordUrl(null);
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
            'index'  => ListStatuses::route('/'),
            'create' => CreateStatus::route('/create'),
            'edit'   => EditStatus::route('/{record}/edit'),
        ];
    }
}
