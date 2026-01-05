<?php
namespace App\Filament\Resources\Donghuas\Schemas;

use App\Console\Commands\CrawlIndexPage;
use App\Models\Source;
use App\Models\Status;
use App\Models\Studio;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Fieldset::make('Uploads')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('cover_external_url')
                                                            ->label('Fetch Cover from URL')
                                                            ->placeholder('https://example.com/donghua-cover.jpg')
                                                            ->suffixAction(
                                                                Action::make('fetchCover')
                                                                    ->icon('heroicon-m-arrow-down-tray')
                                                                    ->color('success')
                                                                    ->action(function ($state, $set) {
                                                                        if (empty($state)) {
                                                                            return;
                                                                        }

                                                                        try {
                                                                            $response = Http::get($state);
                                                                            if (!$response->successful()) {
                                                                                throw new \Exception('URL unreachable');
                                                                            }

                                                                            $extension = pathinfo(parse_url($state, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                                                            $filename  = 'img/donghua-covers/' . Str::random(40) . '.' . $extension;
                                                                            Storage::disk('public')->put($filename, $response->body());
                                                                            $set('image_cover', $filename);
                                                                            $set('cover_external_url', null);

                                                                            Notification::make()
                                                                                ->title('Donghua cover downloaded!')
                                                                                ->success()
                                                                                ->send();
                                                                        } catch (\Exception $e) {
                                                                            Notification::make()
                                                                                ->title('Fetch Failed')
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                    }),
                                                            ),
                                                        FileUpload::make('image_cover')
                                                            ->label('Cover Image')
                                                            ->disk('public')
                                                            ->directory('img/donghua-covers')
                                                            ->visibility('public')
                                                            ->image(),

                                                    ]),
                                                Fieldset::make('Title')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('title_en')->label('Title (EN)')->required()->inlineLabel(),
                                                        TextInput::make('title_zh')->label('Title (CN / Pinyin)')->required()->inlineLabel(),
                                                    ]),
                                                Fieldset::make('Status')
                                                    ->columns(1)
                                                    ->schema([
                                                        Toggle::make('is_active')
                                                            ->label('Active')
                                                            ->required()
                                                            ->default(true)
                                                            ->onColor('success'),
                                                        Toggle::make('is_observed')
                                                            ->label('Currently is Hot!')
                                                            ->required()
                                                            ->default(true)
                                                            ->onColor('danger'),
                                                        Select::make('status_id')
                                                            ->label('Watch Status')
                                                            ->options(Status::query()->pluck('name', 'id'))
                                                            ->required(),
                                                        Select::make('airing')
                                                            ->label('Airing Status')
                                                            ->options([
                                                                '0' => 'Finished',
                                                                '1' => 'Airing',
                                                                '2' => 'Coming Soon',
                                                            ])
                                                            ->required(),
                                                    ]),
                                            ]),
                                    ]),
                                Tab::make('Episode')
                                    ->icon('heroicon-o-hashtag')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Fieldset::make('Episode Number')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('episode_latest')->label('# Latest')->numeric()->inlineLabel(),
                                                        TextInput::make('episode_watched')->label('# Watched (Season)')->numeric()->inlineLabel(),
                                                        TextInput::make('episode_watched_seasonal')->label('# Watches (Overall)')->numeric()->inlineLabel(),
                                                        TextInput::make('episode_total')->label('# Total')->numeric()->inlineLabel(),
                                                    ]),
                                                Fieldset::make('Others')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('season')->numeric()->required()->inlineLabel(),
                                                    ]),
                                            ]),
                                    ]),
                                Tab::make('Sources')
                                    ->icon('heroicon-o-book-open')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->columnSpan('md')
                                            ->schema([
                                                Fieldset::make('MyAnimeList')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('myanimelist')
                                                            ->url()
                                                            ->inlineLabel()
                                                            ->prefixIcon('heroicon-m-globe-alt')
                                                            ->suffixAction(
                                                                Action::make('open_mal')
                                                                    ->label('Visit')
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->color('primary')
                                                                    ->tooltip('Open MyAnimeList in a new tab')
                                                                    ->url(fn($state) => $state)
                                                                    ->openUrlInNewTab()
                                                                    ->visible(fn($state) => !empty($state))
                                                            ),
                                                        Select::make('studio_id')
                                                            ->label('Studio')
                                                            ->options(Studio::orderBy('name')->pluck('name', 'id'))
                                                            ->searchable()
                                                            ->inlineLabel()
                                                            ->relationship('studio', 'name')
                                                            ->preload()
                                                            ->createOptionForm([
                                                                TextInput::make('name')->required()->inlineLabel()->autofocus(),
                                                                TextInput::make('url')->label('URL')->url()->inlineLabel()->autofocus(),
                                                                Toggle::make('is_active')->label('Active')->required()->default(true)->inlineLabel(),
                                                            ]),
                                                        Select::make('source_id')
                                                            ->label('Source')
                                                            ->options(Source::orderBy('name')->pluck('name', 'id'))
                                                            ->searchable()
                                                            ->inlineLabel()
                                                            ->relationship('source', 'name')
                                                            ->preload()
                                                            ->createOptionForm([
                                                                TextInput::make('name')->required()->inlineLabel()->autofocus(),
                                                                Toggle::make('is_active')->label('Active')->required()->default(true)->inlineLabel(),
                                                            ]),
                                                    ]),
                                                Fieldset::make('Wiki')
                                                    ->columns(1)
                                                    ->schema([
                                                        TextInput::make('mc_name')->label('MC Name'),
                                                        TextInput::make('mc_wikia')->label('MC Wiki URL')->url(),
                                                    ]),

                                            ]),
                                    ]),
                                Tab::make('Streaming Links')
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        KeyValue::make('external_titles')
                                            ->label('Details of website streaming links, such as AnimeXin, for crawling purposes.')
                                            ->keyLabel('Name')
                                            ->valueLabel('Description')
                                            ->reorderable()
                                            ->default([
                                                'animexin_title'      => '',
                                                'animexin_url'        => '',
                                                'animekhor_title'     => '',
                                                'animekhor_url'       => '',
                                                'donghuastream_title' => '',
                                                'donghuastream_url'   => '',
                                                'donghuaworld_title'  => '',
                                                'donghuaworld_url'    => '',
                                            ]),
                                    ]),
                                Tab::make('Crawl Index')
                                    ->icon('heroicon-o-arrow-path')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                Fieldset::make('AnimeXin')
                                                    ->columns(1)
                                                    ->schema([
                                                        Action::make('runCrawlAx')
                                                            ->label('Crawl Episode Index')
                                                            ->icon('heroicon-o-arrow-path')
                                                            ->color('success')
                                                            ->action(function ($record) {
                                                                // Run the command
                                                                Artisan::call(CrawlIndexPage::class, [
                                                                    'website'      => 'animexin',
                                                                    '--donghua_id' => $record->id, // You can pass dynamic IDs here
                                                                ]);

                                                                // Show a success message
                                                                Notification::make()
                                                                    ->title('Crawl completed successfully.')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                            ->requiresConfirmation()
                                                            ->modalHeading('Run Crawler on AnimeXin')
                                                            ->modalDescription('Are you sure you want to crawl the AnimeXin index? This process might take a few moments.')
                                                            ->modalSubmitActionLabel('Yes, start crawling')
                                                            ->modalCancelActionLabel('Cancel'),
                                                    ]),
                                                Fieldset::make('AnimeKhor')
                                                    ->columns(1)
                                                    ->schema([
                                                        Action::make('runCrawlAk')
                                                            ->label('Crawl Episode Index')
                                                            ->icon('heroicon-o-arrow-path')
                                                            ->color('info')
                                                            ->action(function ($record) {
                                                                // Run the command
                                                                Artisan::call(CrawlIndexPage::class, [
                                                                    'website'      => 'animekhor',
                                                                    '--donghua_id' => $record->id, // You can pass dynamic IDs here
                                                                ]);

                                                                // Show a success message
                                                                Notification::make()
                                                                    ->title('Crawl completed successfully.')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                            ->requiresConfirmation()
                                                            ->modalHeading('Run Crawler on AnimeKhor')
                                                            ->modalDescription('Are you sure you want to crawl the AnimeKhor index? This process might take a few moments.')
                                                            ->modalSubmitActionLabel('Yes, start crawling')
                                                            ->modalCancelActionLabel('Cancel'),
                                                    ]),
                                                Fieldset::make('DonghuaStream')
                                                    ->columns(1)
                                                    ->schema([
                                                        Action::make('runCrawlDh')
                                                            ->label('Crawl Episode Index')
                                                            ->icon('heroicon-o-arrow-path')
                                                            ->color('danger')
                                                            ->action(function ($record) {
                                                                // Run the command
                                                                Artisan::call(CrawlIndexPage::class, [
                                                                    'website'      => 'donghuastream',
                                                                    '--donghua_id' => $record->id, // You can pass dynamic IDs here
                                                                ]);

                                                                // Show a success message
                                                                Notification::make()
                                                                    ->title('Crawl completed successfully.')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                            ->requiresConfirmation()
                                                            ->modalHeading('Run Crawler on DonghuaStream')
                                                            ->modalDescription('Are you sure you want to crawl the DonghuaStream index? This process might take a few moments.')
                                                            ->modalSubmitActionLabel('Yes, start crawling')
                                                            ->modalCancelActionLabel('Cancel'),
                                                    ]),
                                                Fieldset::make('DonghuaWorld')
                                                    ->columns(1)
                                                    ->schema([
                                                        Action::make('runCrawlDw')
                                                            ->label('Crawl Episode Index')
                                                            ->icon('heroicon-o-arrow-path')
                                                            ->color('warning')
                                                            ->action(function ($record) {
                                                                // Run the command
                                                                Artisan::call(CrawlIndexPage::class, [
                                                                    'website'      => 'donghuaworld',
                                                                    '--donghua_id' => $record->id, // You can pass dynamic IDs here
                                                                ]);

                                                                // Show a success message
                                                                Notification::make()
                                                                    ->title('Crawl completed successfully.')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                            ->requiresConfirmation()
                                                            ->modalHeading('Run Crawler on DonghuaWorld')
                                                            ->modalDescription('Are you sure you want to crawl the DonghuaWorld index? This process might take a few moments.')
                                                            ->modalSubmitActionLabel('Yes, start crawling')
                                                            ->modalCancelActionLabel('Cancel'),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
