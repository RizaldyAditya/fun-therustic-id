<?php

namespace App\Filament\Resources\Avns\Tables;

use App\Models\Status;
use App\Traits\Vndb;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvnsTable
{
    use Vndb;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('vndb_id')
                    ->label('VNDB ID')
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('developer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextInputColumn::make('version')
                    ->label('Latest Version')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->extraHeaderAttributes(['style' => 'width: 100px;']),
                IconColumn::make('play_status')
                    ->label('')
                    ->alignCenter()
                    ->state(function ($record) {
                        return $record->status->slug ?? 'awaiting-update';
                    })
                    ->icons([
                        'heroicon-s-arrow-down-on-square-stack' => 'to-download',
                        'heroicon-s-arrow-left-end-on-rectangle' => 'plan-to-play',
                        'heroicon-s-play' => 'playing',
                        'heroicon-s-play-pause' => 'on-hold',
                        'heroicon-s-arrow-up-on-square-stack' => 'waiting-for-update',
                        'heroicon-s-check' => 'completed',
                        'heroicon-s-x-mark' => 'dropped',
                        'heroicon-s-trash' => 'abandoned',
                    ])
                    ->color(fn (string $state): string => match ($state) {
                        'to-download', 'playing' => 'info',
                        'plan-to-play', 'on-hold' => 'warning',
                        'waiting-for-update', 'completed' => 'success',
                        'dropped', 'abandoned' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        'to-download' => 'To Download',
                        'plan-to-play' => 'Plan to Play',
                        'playing' => 'Playing',
                        'on-hold' => 'On Hold',
                        'waiting-for-update' => 'Waiting Update for New Version',
                        'completed' => 'You Completed this AVN',
                        'dropped' => 'You Dropped this AVN',
                        'abandoned' => 'This AVN has been abandoned',
                        default => '',
                    }),
                SelectColumn::make('status_id')
                    ->label('Status')
                    ->options(Status::all()->pluck('name', 'id'))
                    ->searchableOptions()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('rating')
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return '<span class="text-gray-300">No Rating</span>';
                        }
                        $stars = str_repeat('⭐', $state);
                        $emptyCount = 5 - $state;
                        $empty = '<span class="text-gray-300" style="opacity: 0.5;">'.str_repeat('⭐', $emptyCount).'</span>';

                        return '<div class="flex items-center text-lg leading-none">'.$stars.$empty.'</div>';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('itch_io_url')
                    ->label('itch.io Link')
                    ->url(fn ($record) => $record->itch_io_url)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('cover_image')
                    ->label('Cover Image')
                    ->disk('public')
                    ->visibility('public')
                    ->imageHeight(29)
                    ->alignCenter()
                    ->toggleable()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn ($record) => $record->title.' Cover Image')
                            ->modalDescription(fn ($record) => $record->version ? 'Version: '.$record->version : '')
                            ->modalWidth('4xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('image_preview')->view('filament.image-preview')
                                    ->viewData(fn ($record) => [
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
                        'heroicon-s-check' => true,                 // Shown when state is true
                        'heroicon-s-exclamation-triangle' => false, // Shown when state is false
                    ])
                    ->colors([
                        'success' => true,
                        'danger' => false,
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
                        if (! $latestSave) {
                            return false;
                        }
                        $played = trim(strtolower($record->last_played_version));
                        $saved = trim(strtolower($latestSave->version));

                        return $played === $saved;
                    })
                    ->icons([
                        'heroicon-s-cloud-arrow-up' => true,
                        'heroicon-s-archive-box-x-mark' => false,
                    ])
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->tooltip(function ($record) {
                        $latestSave = $record->saves()->latest('version')->first();
                        if (! $latestSave) {
                            return 'No save files uploaded yet.';
                        }

                        return trim($record->last_played_version) === ($latestSave->version)
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
                    ->query(fn (Builder $query) => $query->whereColumn('version', '!=', 'last_played_version')),
                SelectFilter::make('status_id')->relationship('status', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                // a button to open itch.io url to new tab
                Action::make('view_itch_io')
                    ->label('')
                    ->icon('heroicon-s-arrow-up-right')
                    ->color('success')
                    ->url(fn ($record) => $record->itch_io_url)
                    ->openUrlInNewTab()
                    ->tooltip('Open AVN on itch.io'),
                Action::make('view_vndb')
                    ->label('VNDB')
                    ->color('primary')
                    ->tooltip('Open VNDB Detail')
                    ->modalHeading(fn ($record) => 'VNDB')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('full')
                    ->modalContent(function ($record) {
                        $vndata = Vndb::getVnData($record->vndb_id);
                        $chardata = Vndb::getCharactersData($record->vndb_id);

                        return view('filament.vndb-modal', [
                            'vndb_id' => $record->vndb_id,
                            'title' => $record->title,
                            'image' => $record->cover_image,
                            'vndata' => $vndata,
                            'chardata' => $chardata,
                        ]);
                    }),
                Action::make('view_gallery')
                    ->label('')
                    ->icon('heroicon-s-photo')
                    ->color(fn ($record) => $record->galleries()->exists() ? 'info' : 'danger')
                    ->modalHeading(fn ($record) => "Gallery: {$record->title}")
                    ->modalWidth('7xl')        // Extra wide for the carousel feel
                    ->modalSubmitAction(false) // Hide the "Submit" button
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) => view('filament.gallery-carousel', [
                        'images' => $record->galleries()->orderBy('sort_order')->get(),
                    ]))
                    ->slideOver()
                    ->tooltip(fn ($record) => $record->galleries()->exists() ? 'View AVN Gallery' : 'No gallery uploaded yet.'),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit AVN'),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete AVN'),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->headerActions([
                Action::make('filterPlaying')
                    ->label('Playing')
                    ->icon('heroicon-s-play')
                    ->color('info')
                    ->action(function ($livewire) {
                        $playingStatus = Status::where('slug', '=', 'playing')->first();
                        if ($playingStatus) {
                            $livewire->tableFilters['status_id']['value'] = $playingStatus->id;
                            $livewire->tableFilters['needs_update']['isActive'] = false;
                        }
                    })
                    ->tooltip('Filter AVNs with Playing status'),
                Action::make('filterPlanToPlay')
                    ->label('Plan to Play')
                    ->icon('heroicon-s-arrow-left-end-on-rectangle')
                    ->color('warning')
                    ->action(function ($livewire) {
                        $planToPlayStatus = Status::where('slug', '=', 'plan-to-play')->first();
                        if ($planToPlayStatus) {
                            $livewire->tableFilters['status_id']['value'] = $planToPlayStatus->id;
                            $livewire->tableFilters['needs_update']['isActive'] = false;
                        }
                    })
                    ->tooltip('Filter AVNs with Plan to Play status'),
                Action::make('filterToDownload')
                    ->label('To Download')
                    ->icon('heroicon-s-arrow-down-on-square')
                    ->color('gray')
                    ->action(function ($livewire) {
                        $toDownloadStatus = Status::where('slug', '=', 'to-download')->first();
                        if ($toDownloadStatus) {
                            $livewire->tableFilters['status_id']['value'] = $toDownloadStatus->id;
                            $livewire->tableFilters['needs_update']['isActive'] = false;
                        }
                    })
                    ->tooltip('Filter AVNs with To Download status'),
                Action::make('filterDropped')
                    ->label('Dropped')
                    ->icon('heroicon-s-x-circle')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $droppedStatus = Status::where('slug', '=', 'dropped')->first();
                        if ($droppedStatus) {
                            $livewire->tableFilters['status_id']['value'] = $droppedStatus->id;
                            $livewire->tableFilters['needs_update']['isActive'] = false;
                        }
                    }),
                Action::make('filterAbandoned')
                    ->label('Abandoned')
                    ->icon('heroicon-s-x-circle')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $abandonedStatus = Status::where('slug', '=', 'abandoned')->first();
                        if ($abandonedStatus) {
                            $livewire->tableFilters['status_id']['value'] = $abandonedStatus->id;
                            $livewire->tableFilters['needs_update']['isActive'] = false;
                        }
                    }),
                Action::make('newUpdate')
                    ->label('New Update')
                    ->icon('heroicon-s-arrow-path')
                    ->color('info')
                    ->action(function ($livewire) {
                        $livewire->tableFilters['needs_update']['isActive'] = true;
                    }),
                Action::make('clearFilters')
                    ->label('Clear Filters')
                    ->icon('heroicon-s-x-mark')
                    ->color('gray')
                    ->action(function ($livewire) {
                        $livewire->tableFilters['status_id']['value'] = null;
                        $livewire->tableFilters['needs_update']['isActive'] = false;
                    })
                    ->tooltip('Clear status filters'),
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
            ->defaultSort('title')
            ->paginated([10, 20, 30, 50, 100]);
    }
}
