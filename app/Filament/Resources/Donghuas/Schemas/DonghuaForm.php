<?php
namespace App\Filament\Resources\Donghuas\Schemas;

use Filament\Forms;
use App\Models\Source;
use App\Models\Status;
use App\Models\Studio;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class DonghuaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(1)
                    ->columnSpan('full')
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make('Title')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Forms\Components\TextInput::make('title_en')->label('Title (English)')->required(),
                                                Forms\Components\TextInput::make('title_zh')->label('Title (Chinese)')->required(),
                                                Forms\Components\FileUpload::make('image_cover')
                                                    ->disk('public')
                                                    ->directory('img/covers')
                                                    ->required()
                                                    ->label('Cover Image')
                                                    ->visibility('public')
                                                    ->image(),
                                                Forms\Components\Toggle::make('is_active')->required()->default(true),
                                            ])
                                    ])
                                    ->icon(Heroicon::PencilSquare),
                                Tab::make('Episode')
                                    ->schema([
                                        Grid::make()
                                            ->columns(5)
                                            ->columnSpan('md')
                                            ->schema([
                                                Forms\Components\TextInput::make('season')->numeric()->required(),
                                                Forms\Components\TextInput::make('episode_latest')->numeric(),
                                                Forms\Components\TextInput::make('episode_watched')->numeric(),
                                                Forms\Components\TextInput::make('episode_watched_seasonal')->numeric(),
                                                Forms\Components\TextInput::make('episode_total')->numeric(),
                                            ])
                                    ])
                                    ->icon(Heroicon::PercentBadge),
                                Tab::make('Status')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Forms\Components\Select::make('status_id')
                                                    ->options(Status::query()->pluck('name', 'id'))
                                                    ->required(),
                                                Forms\Components\Select::make('airing')
                                                    ->options([
                                                        '0' => 'Finished',
                                                        '1' => 'Airing',
                                                    ])
                                                    ->required(),
                                            ])
                                    ])
                                    ->icon(Heroicon::CheckBadge),
                                Tab::make('Sources')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Forms\Components\Select::make('studio_id')
                                                    ->label('Studio')
                                                    ->options(Studio::orderBy('name')->pluck('name', 'id'))
                                                    ->searchable(),
                                                Forms\Components\Select::make('source_id')
                                                    ->label('Source')
                                                    ->options(Source::orderBy('name')->pluck('name', 'id'))
                                                    ->searchable(),
                                            ])
                                    ])
                                    ->icon(Heroicon::SquaresPlus),
                                Tab::make('MAL & Wiki')
                                    ->schema([
                                        Forms\Components\TextInput::make('myanimelist')->url()->required(),
                                        Forms\Components\TextInput::make('mc_name')->label('MC Name'),
                                        Forms\Components\TextInput::make('mc_wikia')->label('MC Wiki URL')->url(),
                                    ])
                                    ->icon(Heroicon::BookOpen),
                                Tab::make('External Links')
                                    ->schema([
                                        Forms\Components\KeyValue::make('external_titles')
                                            ->keyLabel('Name')
                                            ->valueLabel('Description')
                                            ->reorderable(),
                                    ])
                                    ->icon(Heroicon::Link),
                            ]),
                    ]),
            ]);
    }
}
