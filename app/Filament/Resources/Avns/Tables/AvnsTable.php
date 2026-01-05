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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->label('Latest Version')
                    ->sortable()
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
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '<span class="text-gray-300">No Rating</span>';
                        }
                        $stars      = str_repeat('⭐', $state);
                        $emptyCount = 5 - $state;
                        $empty      = '<span class="text-gray-300" style="opacity: 0.5;">' . str_repeat('⭐', $emptyCount) . '</span>';
                        return '<div class="flex items-center text-lg leading-none">' . $stars . $empty . '</div>';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->imageHeight(29)
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title . ' Cover Image')
                            ->modalDescription(fn($record) => $record->version ? 'Version: ' . $record->version : '')
                            ->modalWidth('4xl')
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
                IconColumn::make('version_status')
                    ->label('')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        return $record->version === $record->last_played_version;
                    })
                    ->icons([
                        'heroicon-s-check-badge'        => true,  // Shown when state is true
                        'heroicon-s-exclamation-circle' => false, // Shown when state is false
                    ])
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ])
                    ->tooltip(function ($record) {
                        return $record->version === $record->last_played_version
                            ? "You've caught up! ({$record->version})"
                            : "There is an update! (Played: {$record->last_played_version} -> Latest: {$record->version})";
                    }),
                IconColumn::make('save_status')
                    ->label('')
                    ->alignCenter()
                    ->state(static function ($record): bool {
                        $latestSave = $record->saves()->latest('version')->first();
                        if (!$latestSave) {
                            return false;
                        }
                        $played = trim(strtolower($record->last_played_version));
                        $saved  = trim(strtolower($latestSave->version));
                        return $played === $saved;
                    })
                    ->icons([
                        'heroicon-s-cloud-arrow-up'     => true,
                        'heroicon-s-exclamation-circle' => false,
                    ])
                    ->colors([
                        'success' => true,
                        'danger'  => false,
                    ])
                    ->tooltip(function ($record) {
                        $latestSave = $record->saves()->latest('version')->first();
                        if (!$latestSave) {
                            return "No save files uploaded yet.";
                        }
                        return $record->last_played_version === $latestSave->label
                            ? "Cloud save is up to date ({$latestSave->version})"
                            : "Cloud save ({$latestSave->version}) is older than your last played version ({$record->last_played_version})";
                    }),
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
                Filter::make('needs_update')
                    ->label('Needs Update')
                    ->query(function (Builder $query) {
                        return $query->whereColumn('version', '!=', 'last_played_version');
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('view_gallery')
                    ->label('')
                    ->icon('heroicon-s-photo')
                    ->color(fn($record) => $record->galleries_count > 0 ? 'info' : 'danger')
                    ->icon('heroicon-s-photo')
                    ->modalHeading(fn($record) => "Gallery: {$record->title}")
                    ->modalWidth('7xl')        // Extra wide for the carousel feel
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.gallery-carousel', [
                        'images' => $record->gallery()->orderBy('sort_order')->get(),
                    ]))
                    ->slideOver()
                    ->tooltip(fn($record) => $record->galleries_count > 0 ? 'View Gallery' : 'No gallery uploaded yet'),
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
