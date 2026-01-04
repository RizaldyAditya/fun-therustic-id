<?php
namespace App\Filament\Resources\Avns\Tables;

use App\Models\Status;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

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
                TextColumn::make('rating')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '<span class="text-gray-300">No Rating</span>';
                        }
                        $stars = str_repeat('⭐', $state);
                        $emptyCount = 5 - $state;
                        $empty = '<span class="text-gray-300" style="opacity: 0.5;">' . str_repeat('⭐', $emptyCount) . '</span>';
                        return '<div class="flex items-center text-lg leading-none">' . $stars . $empty . '</div>';
                    }),
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
                    ->toggleable(isToggledHiddenByDefault: true)
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
                TextInputColumn::make('last_played_version')
                    ->label('Last Played Version')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->extraHeaderAttributes(['style' => 'width: 100px;']),
                TextColumn::make('saves_file_url')
                    ->label('Last Saves File')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->formatStateUsing(fn($state) => $state ? 'Go to GDrive' : 'No File')
                    ->url(function ($record) {
                        if (!$record->saves_file_url) {
                            return null;
                        }
                        return "https://drive.google.com/file/d/{$record->saves_file_url}/view?usp=sharing";
                    })
                    ->openUrlInNewTab(),
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
