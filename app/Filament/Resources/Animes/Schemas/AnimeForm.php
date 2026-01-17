<?php

namespace App\Filament\Resources\Animes\Schemas;

use App\Models\Genre;
use App\Models\Source;
use App\Models\Studio;
use App\Services\JikanService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnimeForm
{
    /**
     * Get or create a studio by name and return its ID.
     */
    private static function getOrCreateStudio(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }
        $studio = Studio::firstOrCreate(['name' => $name]);
        return $studio->id;
    }

    /**
     * Get or create a source by name and return its ID.
     */
    private static function getOrCreateSource(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }
        $source = Source::firstOrCreate(['name' => $name]);
        return $source->id;
    }

    /**
     * Get or create genres by names and return their IDs array.
     */
    private static function getOrCreateGenres(array $genres): array
    {
        $genreIds = [];
        foreach ($genres as $genre) {
            $name = is_array($genre) ? $genre['name'] : $genre;
            if (!empty($name)) {
                $genreModel = Genre::firstOrCreate(['name' => $name]);
                $genreIds[] = $genreModel->id;
            }
        }
        return $genreIds;
    }

    public static function configure(Schema $schema): Schema
    {
        $downloadPoster = function ($state, $set) {
            if (!$state) {
                return;
            }

            try {
                $response = Http::get($state);
                if ($response->successful()) {
                    $path = 'img/anime-covers/' . Str::random(40) . '.jpg';
                    Storage::disk('public')->put($path, $response->body());
                    $set('poster', $path);
                    Notification::make()->title('Poster Image Downloaded!')->success()->send();
                }
            } catch (\Exception $e) {
                // Handle error
            }
        };

        return $schema
            ->components([
                Grid::make()
                    ->columns(1)
                    ->columnSpan('full')
                    ->schema([
                        Tabs::make()
                            ->tabs([
                                Tab::make('Details')
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('full')
                                            ->schema([
                                                Fieldset::make('Information')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('myanimelist_url')
                                                            ->label('MyAnimeList URL')
                                                            ->url()
                                                            ->unique()
                                                            ->prefixAction(
                                                                Action::make('openMyanimelist')
                                                                    ->url(fn($state) => 'https://myanimelist.net/')
                                                                    ->icon('heroicon-s-globe-alt')
                                                                    ->tooltip('Open myanimelist.net in new tab')
                                                                    ->openUrlInNewTab()
                                                            )
                                                            ->inlineLabel()
                                                            ->placeholder('https://myanimelist.net/anime/xxx/...?q=...&cat=...')
                                                            ->suffixAction(
                                                                Action::make('openMyanimelistOfThisAnime')
                                                                    ->url(fn($record) => $record->myanimelist_url ?? null)
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->tooltip('Open this anime myanimelist.net of this anime in new tab')
                                                                    ->openUrlInNewTab()
                                                                    ->hidden(fn($state) => empty($state)),
                                                            )
                                                            ->suffixAction(
                                                                Action::make('fetchFromJikan')
                                                                    ->icon('heroicon-s-cloud-arrow-down')
                                                                    ->tooltip('Fetch from Jikan API')
                                                                    ->action(function ($state, $set, $get) use ($downloadPoster) {
                                                                        if (empty($state)) {
                                                                            return;
                                                                        }

                                                                        $jikan     = new JikanService();
                                                                        $animeData = $jikan->getAnimeData($state);

                                                                        if ($animeData) {
                                                                            // Studio: auto-create if not exists, then set ID
                                                                            $studioName = $animeData['studios'][0]['name'] ?? null;
                                                                            if ($studioName) {
                                                                                $studio = Studio::firstOrCreate(['name' => $studioName]);
                                                                                $set('studio_id', $studio->id);
                                                                            }

                                                                            // Source: auto-create if not exists, then set ID
                                                                            $sourceName = $animeData['source'] ?? null;
                                                                            if ($sourceName) {
                                                                                $source = Source::firstOrCreate(['name' => $sourceName]);
                                                                                $set('source_id', $source->id);
                                                                            }

                                                                            // Genres: auto-create if not exists, then set IDs
                                                                            if (!empty($animeData['genres'])) {
                                                                                $genreIds = self::getOrCreateGenres($animeData['genres']);
                                                                                $set('genre_id', $genreIds);
                                                                            }

                                                                            $set('title', trim($animeData['title']) ?? '');
                                                                            $set('title_jp', trim($animeData['title_jp']) ?? '');
                                                                            $set('synopsis', $animeData['synopsis']);
                                                                            $set('poster_url', $animeData['poster']);
                                                                            $downloadPoster($animeData['poster'], $set);
                                                                            $set('type', trim($animeData['type']) ?? '');
                                                                            $set('season', $animeData['season']);
                                                                            $set('year', $animeData['year']);
                                                                            $set('broadcast_day', strtolower(trim($animeData['broadcast_day']) ?? '') ?? '');
                                                                            $set('episode_total', $animeData['episodes']);
                                                                            $set('myanimelist_score', $animeData['myanimelist_score']);
                                                                            $set('air_date', $animeData['aired']['prop']['from']['year']
                                                                                . '-' . $animeData['aired']['prop']['from']['month']
                                                                                . '-' . $animeData['aired']['prop']['from']['day']);
                                                                            $set('is_airing', $animeData['is_airing']);

                                                                            // extra attributes
                                                                            if (isset($animeData['theme']) && isset($animeData['external'])) {
                                                                                $currentAttributes = $get('attributes') ?? [];
                                                                                $newAttributes     = [];
                                                                                $no                = 1;
                                                                                if (count($animeData['theme']['openings']) > 0) {
                                                                                    foreach ($animeData['theme']['openings'] as $opening) {
                                                                                        $newAttributes['Soundtrack OP ' . $no] = $opening;
                                                                                        $no++;
                                                                                    }
                                                                                }
                                                                                if (count($animeData['theme']['endings']) > 0) {
                                                                                    foreach ($animeData['theme']['endings'] as $ending) {
                                                                                        $newAttributes['Soundtrack ED ' . $no] = $ending;
                                                                                        $no++;
                                                                                    }
                                                                                }
                                                                                if (count($animeData['external']) > 0) {
                                                                                    foreach ($animeData['external'] as $external) {
                                                                                        $newAttributes[$external['name']] = $external['url'];
                                                                                    }
                                                                                }
                                                                                $set('attributes', array_merge($currentAttributes, $newAttributes));
                                                                            }

                                                                            Notification::make()
                                                                                ->title('Anime data imported!')
                                                                                ->success()
                                                                                ->send();
                                                                        } else {
                                                                            Notification::make()
                                                                                ->title('Failed to fetch data')
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                    })
                                                            ),
                                                        TextInput::make('title')->label('Title (EN)')->required()->inlineLabel()->autofocus(),
                                                        TextInput::make('title_jp')->label('Title (JP)')->inlineLabel(),
                                                        Select::make('type')
                                                            ->options(config('constant.anime_type'))
                                                            ->default('TV')
                                                            ->required()
                                                            ->inlineLabel(),
                                                        Select::make('genre_id')
                                                            ->relationship('genre', 'name')
                                                            ->multiple()
                                                            ->preload()
                                                            ->searchable()
                                                            ->placeholder('Select Genres')
                                                            ->inlineLabel()
                                                            ->visible(fn(callable $get) => $get('genre_id') !== null)
                                                            ->getOptionLabelsUsing(fn($values) => Genre::whereIn('id', $values)
                                                                    ->pluck('name', 'id')
                                                                    ->toArray() + array_combine($values, $values)
                                                            ),
                                                        Select::make('status_id')
                                                            ->relationship('status', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->placeholder('Select Status')
                                                            ->inlineLabel(),
                                                        Toggle::make('is_airing')
                                                            ->label('Currently Airing')
                                                            ->default(false),
                                                        Toggle::make('is_active')
                                                            ->label('Status')
                                                            ->default(true),
                                                        Textarea::make('synopsis')
                                                            ->columnSpanFull()
                                                            ->rows(12),
                                                    ]),
                                                Fieldset::make('Uploads & Sources')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('poster_url')
                                                            ->label('Fetch Poster from URL')
                                                            ->placeholder('https://example.com/image.jpg')
                                                            ->afterStateUpdated($downloadPoster)
                                                            ->suffixAction(
                                                                Action::make('download')
                                                                    ->icon('heroicon-m-arrow-down-tray')
                                                                    ->action(fn($state, $set) => $downloadPoster($state, $set))
                                                            ),
                                                        FileUpload::make('poster')
                                                            ->label('Poster Image')
                                                            ->disk('public')
                                                            ->directory('img/anime-covers')
                                                            ->visibility('public')
                                                            ->image()
                                                            ->helperText('You can upload manually or use the URL fetcher above.'),
                                                        Select::make('studio_id')
                                                            ->relationship('studio', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->inlineLabel()
                                                            ->getOptionLabelsUsing(fn($value) => Studio::find($value)?->name ?? $value),
                                                        Select::make('source_id')
                                                            ->relationship('source', 'name')
                                                            ->preload()
                                                            ->searchable()
                                                            ->inlineLabel(),
                                                        TextInput::make('myanimelist_score')
                                                            ->label('MyAnimeList Score')
                                                            ->numeric()
                                                            ->inlineLabel(),
                                                        DatePicker::make('air_date')
                                                            ->label('Aired Date')
                                                            ->inlineLabel(),
                                                    ]),
                                            ]),
                                    ]),
                                Tab::make('Episodes')
                                    ->icon('heroicon-s-hashtag')
                                    ->schema([
                                        Grid::make(2)
                                            ->columnSpan('full')
                                            ->schema([
                                                Fieldset::make('Episode Information')
                                                    ->columns(1)
                                                    ->schema([
                                                        Select::make('season')
                                                            ->options(config('constant.season_name'))
                                                            ->default('Spring')
                                                            ->inlineLabel(),
                                                        TextInput::make('year')
                                                            ->numeric()
                                                            ->inlineLabel(),
                                                        Select::make('broadcast_day')
                                                            ->options([
                                                                'mondays' => 'Monday',
                                                                'tuesdays' => 'Tuesday',
                                                                'wednesdays' => 'Wednesday',
                                                                'thursdays' => 'Thursday',
                                                                'fridays' => 'Friday',
                                                                'saturdays' => 'Saturday',
                                                                'sundays' => 'Sunday',
                                                            ])
                                                            ->inlineLabel(),
                                                        TextInput::make('episode_total')
                                                            ->label('Total')
                                                            ->numeric()
                                                            ->inlineLabel(),
                                                        TextInput::make('episode_watched')
                                                            ->label('Watched')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->inlineLabel(),
                                                        TextInput::make('episode_downloaded')
                                                            ->label('Downloaded')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->inlineLabel(),
                                                    ]),
                                            ]),
                                    ]),
                                Tab::make('Extras Attributes')
                                    ->icon('heroicon-s-cog')
                                    ->schema([
                                        KeyValue::make('attributes')
                                            ->label('Other details you want to add..')
                                            ->keyLabel('Name')
                                            ->valueLabel('Description')
                                            ->reorderable(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
