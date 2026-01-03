<?php
namespace App\Filament\Resources\Avns\Tables;

use App\Models\Status;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Columns\TextInputColumn;

class AvnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('developer')
                    ->searchable()
                    ->toggleable(),
                TextInputColumn::make('version')
                    ->searchable()
                    ->toggleable()
                    ->extraHeaderAttributes(['style' => 'width: 100px;']),
                SelectColumn::make('status.name')
                    ->label('Status')
                    ->options(Status::query()->pluck('name', 'id'))
                    ->searchableOptions()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('itch_io_url')
                    ->label('itch.io Link')
                    ->url(fn($record) => $record->itch_io_url)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                ImageColumn::make('cover_image')
                    ->label('Cover Image')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->toggleable()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title . ' Cover Image')
                            ->modalDescription(fn($record) => $record->version ? 'Version: ' . $record->version : '')
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record?->cover_image,
                                    ]),
                            ])
                    ),
                TextColumn::make('last_updated_on_itch')
                    ->label('Last Updated on itch.io')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit'),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete'),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                        RestoreBulkAction::make(),
                        ForceDeleteBulkAction::make(),
                    ]),
                ]),
            ])
            ->recordUrl(null)
            ->defaultSort('title');
    }
}
