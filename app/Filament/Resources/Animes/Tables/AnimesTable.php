<?php

namespace App\Filament\Resources\Animes\Tables;

use App\Models\Status;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('poster')
                    ->disk('public')
                    ->visibility('public')
                    ->alignCenter()
                    ->action(
                        Action::make('preview')
                            ->modalHeading(fn($record) => $record->title . ' Episode List')
                            ->modalDescription(fn($record) => $record->title_jp)
                            ->modalWidth('2xl')
                            ->modalSubmitAction(false)
                            ->schema([
                                ViewField::make('image_preview')->view('filament.image-preview')
                                    ->viewData(fn($record) => [
                                        'image' => $record?->poster,
                                    ]),
                            ])
                    ),
                TextColumn::make('title')
                    ->description(fn($record) => $record->title_jp)
                    ->sortable()
                    ->searchable()
                    ->alignStart(),
                TextColumn::make('type')->badge()->sortable()->alignCenter()->toggleable(),
                SelectColumn::make('status_id')
                    ->label('Status')
                    ->options(Status::query()->pluck('name', 'id'))
                    ->searchableOptions()
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'width: 200px; text-align: center;']),
                TextColumn::make('season')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(function ($record) {
                        return match ($record->season) {
                            'winter' => 'Winter' . ' ' . $record->year,
                            'spring' => 'Spring' . ' ' . $record->year,
                            'summer' => 'Summer' . ' ' . $record->year,
                            'fall' => 'Fall' . ' ' . $record->year,
                        };
                    })
                    ->toggleable(),
                ColumnGroup::make('Episode')
                    ->columns([
                        TextColumn::make('episode_total')->label('# Total')->sortable()->alignCenter()->toggleable(),
                        TextInputColumn::make('episode_watched')->label('# Watched')->type('number')->sortable()->alignCenter()->width(100)->toggleable(),
                        TextInputColumn::make('episode_downloaded')->label('# Downloaded')->type('number')->sortable()->alignCenter()->width(100)->toggleable(),
                    ]),
                TextColumn::make('broadcast_day')->label('Broadcast Day')->alignCenter()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        return match ($record->broadcast_day) {
                            'mondays' => 'Mondays',
                            'tuesdays' => 'Tuesdays',
                            'wednesdays' => 'Wednesdays',
                            'thursdays' => 'Thursdays',
                            'fridays' => 'Fridays',
                            'saturdays' => 'Saturdays',
                            'sundays' => 'Sundays',
                        };
                    })
                    ->toggleable(),
                TextColumn::make('myanimelist_score')
                    ->label('MAL Score')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('air_date')
                    ->label('Premier Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                ColumnGroup::make('status')
                    ->label('Status')
                    ->columns([
                        ToggleColumn::make('is_hot')
                            ->label('Hot')
                            ->sortable()
                            ->alignCenter()
                            ->toggleable(),
                        ToggleColumn::make('is_active')
                            ->label('Active')
                            ->sortable()
                            ->alignCenter()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'TV' => 'TV',
                        'OVA' => 'OVA',
                        'Movie' => 'Movie',
                    ]),
                SelectFilter::make('season')
                    ->options([
                        'winter' => 'Winter',
                        'spring' => 'Spring',
                        'summer' => 'Summer',
                        'fall' => 'Fall',
                    ]),
                Filter::make('year_filter')
                    ->schema([
                        TextInput::make('year')
                            ->numeric()
                            ->placeholder('Enter year (e.g., 2026)')
                            ->maxLength(4),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['year'])) {return $query;}
                        return $query->where('year', $data['year']);
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['year']) {
                            return null;
                        }
                        return 'Year: ' . $data['year'];
                    }),
                SelectFilter::make('broadcast_day')
                    ->options([
                        'mondays' => 'Mondays',
                        'tuesdays' => 'Tuesdays',
                        'wednesdays' => 'Wednesdays',
                        'thursdays' => 'Thursdays',
                        'fridays' => 'Fridays',
                        'saturdays' => 'Saturdays',
                        'sundays' => 'Sundays',
                    ]),
            ])
            ->recordActions([
                Action::make('extraAttributes')
                    ->label('')
                    ->icon('heroicon-s-newspaper')
                    ->color('success')
                    ->modalWidth('7xl')
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->title_jp)
                    ->schema([
                        KeyValueEntry::make('attributes')
                            ->keyLabel('Attribute')
                            ->valueLabel('Description'),
                    ])
                    ->modalSubmitAction(false)
                    ->tooltip('Extra Informations')
                    ->slideOver()
                ,
                Action::make('openMyAnimeListUrl')
                    ->label('')
                    ->url(fn($record) => $record->myanimelist_url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-s-arrow-up-right')
                    ->color('info')
                    ->tooltip('Open MyAnimeList URL'),
                EditAction::make()->label('')->tooltip('Edit Anime'),
                DeleteAction::make()->label('')->tooltip('Delete Anime'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
