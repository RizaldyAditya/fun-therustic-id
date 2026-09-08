<?php

namespace App\Filament\Resources\Avns\Schemas;

use App\Traits\Vndb;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvnForm
{
    use Vndb;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        Tabs::make('General')
                            ->tabs([
                                Tab::make('Details')
                                    ->icon(Heroicon::DocumentMagnifyingGlass)
                                    ->columns(2)
                                    ->schema([
                                        Grid::make()
                                            ->columns(1)
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        TextInput::make('vndb_id')
                                                            ->label('VNDB ID')
                                                            ->inlineLabel()
                                                            ->unique()
                                                            ->autofocus(),
                                                        TextInput::make('cover_external_url')
                                                            ->label('Fetch Cover Image from URL')
                                                            ->placeholder('https://example.com/cover.jpg')
                                                            ->helperText('Paste a URL and click the download icon to set as cover.')
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
                                                                            if (! $response->successful()) {
                                                                                throw new \Exception('URL unreachable');
                                                                            }

                                                                            $extension = pathinfo(parse_url($state, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                                                            $filename = 'img/avn-covers/'.Str::random(40).'.'.$extension;
                                                                            Storage::disk('public')->put($filename, $response->body());
                                                                            $set('cover_image', $filename);
                                                                            $set('cover_external_url', null);

                                                                            Notification::make()
                                                                                ->title('Cover downloaded!')
                                                                                ->success()
                                                                                ->send();

                                                                        } catch (\Exception $e) {
                                                                            Notification::make()
                                                                                ->title('Fetch Failed')
                                                                                ->body($e->getMessage())
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                    }),
                                                            ),
                                                        FileUpload::make('cover_image')
                                                            ->label('Cover Image')
                                                            ->image()
                                                            ->disk('public')
                                                            ->visibility('public')
                                                            ->directory('img/avn-covers')
                                                            ->inlineLabel(),
                                                    ]),
                                            ]),
                                        Grid::make()
                                            ->columns(1)
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        TextInput::make('title')
                                                            ->inlineLabel()
                                                            ->required(),
                                                        TextInput::make('developer')
                                                            ->inlineLabel()
                                                            ->required(),
                                                        TextInput::make('version')
                                                            ->required()
                                                            ->inlineLabel(),
                                                        TextInput::make('last_played_version')
                                                            ->label('Last Played Version')
                                                            ->inlineLabel(),
                                                        TextInput::make('itch_io_url')
                                                            ->label('itch.io URL')
                                                            ->url()
                                                            ->inlineLabel()
                                                            ->prefixIcon('heroicon-m-globe-alt')
                                                            ->suffixAction(
                                                                Action::make('visit_itch')
                                                                    ->label('Visit')
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->color('primary')
                                                                    ->tooltip('Open in new tab')
                                                                    ->url(fn ($state) => $state)
                                                                    ->openUrlInNewTab()
                                                                    ->visible(fn ($state) => ! empty($state))
                                                            ),
                                                        TextInput::make('socigames_url')
                                                            ->label('SociGames URL')
                                                            ->url()
                                                            ->inlineLabel()
                                                            ->prefixIcon('heroicon-m-globe-alt')
                                                            ->suffixAction(
                                                                Action::make('visit_socigames')
                                                                    ->label('Visit')
                                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                                    ->color('primary')
                                                                    ->tooltip('Open in new tab')
                                                                    ->url(fn ($state) => $state)
                                                                    ->openUrlInNewTab()
                                                                    ->visible(fn ($state) => ! empty($state))
                                                            ),
                                                        Select::make('rating')
                                                            ->label('Rating')
                                                            ->options([
                                                                5 => '⭐⭐⭐⭐⭐ (Excellent)',
                                                                4 => '⭐⭐⭐⭐ (Good)',
                                                                3 => '⭐⭐⭐ (Average)',
                                                                2 => '⭐⭐ (Poor)',
                                                                1 => '⭐ (Terrible)',
                                                            ])
                                                            ->default(4)
                                                            ->inlineLabel()
                                                            ->native(false),
                                                        Select::make('genre_id')
                                                            ->label('Genres')
                                                            ->inlineLabel()
                                                            ->relationship('genres', 'name')
                                                            ->multiple()
                                                            ->preload()
                                                            ->placeholder('Select Genres')
                                                            ->searchable()
                                                            ->visible(fn (callable $get) => $get('genre_id') !== null),
                                                    ]),
                                            ]),
                                        Grid::make()
                                            ->columns(1)
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        Select::make('status_id')
                                                            ->relationship('status', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->inlineLabel()
                                                            ->required(),
                                                        DatePicker::make('last_updated_on_itch')
                                                            ->label('Last Updated on itch.io')
                                                            ->inlineLabel(),
                                                    ]),
                                            ]),
                                        Grid::make()
                                            ->columnSpanFull()
                                            ->schema([
                                                Section::make()
                                                    ->columnSpanFull()
                                                    ->schema([
                                                        Textarea::make('description')
                                                            ->label('Description / Short Synopsis')
                                                            ->rows(10),
                                                    ]),
                                            ]),
                                    ]),
                                Tab::make('Galleries')
                                    ->icon(Heroicon::Photo)
                                    ->schema([
                                        Repeater::make('gallery')
                                            ->relationship('galleries')
                                            ->grid(4)
                                            ->reorderable('sort_order')
                                            ->reorderableWithButtons()
                                            ->orderColumn('sort_order')
                                            ->addActionLabel('Add New Image Uploader')
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('external_url')
                                                    ->label('Fetch Image from URL')
                                                    ->placeholder('https://example.com/image.jpg')
                                                    ->suffixAction(
                                                        Action::make('fetchImage')
                                                            ->icon('heroicon-m-arrow-down-tray')
                                                            ->color('success')
                                                            ->action(function ($state, $set, $component) {
                                                                if (empty($state)) {
                                                                    return;
                                                                }

                                                                try {
                                                                    $response = Http::get($state);
                                                                    if (! $response->successful()) {
                                                                        throw new \Exception('URL unreachable');
                                                                    }

                                                                    $extension = pathinfo(parse_url($state, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                                                    $filename = 'img/avn-gallery/'.Str::random(40).'.'.$extension;
                                                                    Storage::disk('public')->put($filename, $response->body());
                                                                    $set('image_url', $filename);
                                                                    $set('external_url', null);
                                                                } catch (\Exception $e) {
                                                                    Notification::make()
                                                                        ->title('Fetch Failed')
                                                                        ->danger()
                                                                        ->send();
                                                                }
                                                            })
                                                    ),
                                                TextInput::make('description')
                                                    ->label('Image Description')
                                                    ->placeholder('e.g. Chapter 1 Start'),
                                                FileUpload::make('image_url')
                                                    ->label('Gallery Image')
                                                    ->disk('public')
                                                    ->directory('img/avn-gallery')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                Tab::make('Saves')
                                    ->icon(Heroicon::FolderOpen)
                                    ->schema([
                                        Repeater::make('saves')
                                            ->relationship('saves', function ($query) {
                                                return $query->orderBy('version', 'desc');
                                            })
                                            ->reorderable('sort')
                                            ->orderColumn('sort')
                                            ->reorderableWithButtons()
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('version')
                                                    ->placeholder('e.g. v1.0.0')
                                                    ->required()
                                                    ->inlineLabel(),
                                                TextInput::make('label')
                                                    ->placeholder('e.g. Chapter 1 Start')
                                                    ->inlineLabel(),
                                                DatePicker::make('completed_at')
                                                    ->label('Completed On')
                                                    ->required()
                                                    ->inlineLabel(),
                                                TextInput::make('google_drive_id')
                                                    ->label('Google Drive ID')
                                                    ->placeholder('ID will appear after saving...')
                                                    ->formatStateUsing(fn ($record) => $record?->file_url)
                                                    ->readOnly()
                                                    ->hidden(fn ($state) => empty($state))
                                                    ->prefixIcon(fn ($state) => $state ? 'heroicon-m-check-badge' : null)
                                                    ->prefixIconColor('success')
                                                    ->suffixAction(
                                                        Action::make('open_drive')
                                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                                            ->color('primary')
                                                            ->tooltip('View on Google Drive')
                                                            ->url(fn ($state) => $state ? "https://drive.google.com/file/d/{$state}/view" : null)
                                                            ->openUrlInNewTab()
                                                            ->visible(fn ($state) => ! empty($state))
                                                    ),
                                                Textarea::make('description')->rows(3),
                                                FileUpload::make('file_url')
                                                    ->label('Re/Upload ZIP File')
                                                    ->disk('google')
                                                    ->directory(config('filesystems.disks.google.folderName'))
                                                    ->acceptedFileTypes(['application/zip'])
                                                    ->required(fn ($record) => $record === null)
                                                    ->preserveFilenames()
                                                    ->live()
                                                    ->hidden(fn ($record) => ! empty($record?->file_url))
                                                    ->dehydrateStateUsing(function ($state) {
                                                        if (blank($state)) {
                                                            return null;
                                                        }
                                                        if (! str_contains($state, '/')) {
                                                            return $state;
                                                        }

                                                        return $state;
                                                    }),
                                            ])
                                            ->grid(3)
                                            ->addActionLabel('Add New Save File Uploader'),
                                    ]),
                                Tab::make('Walkthroughs')
                                    ->icon('heroicon-m-book-open')
                                    ->schema([
                                        Repeater::make('walkthroughs')
                                            ->relationship('walkthroughs')
                                            ->reorderable('sort')
                                            ->orderColumn('sort')
                                            ->reorderableWithButtons()
                                            ->defaultItems(0)
                                            ->schema([
                                                TextInput::make('label')
                                                    ->placeholder('e.g. Chapter 1 Start')
                                                    ->inlineLabel(),
                                                TextInput::make('google_drive_id')
                                                    ->label('Google Drive ID')
                                                    ->placeholder('ID will appear after saving...')
                                                    ->formatStateUsing(fn ($record) => $record?->file_url)
                                                    ->readOnly()
                                                    ->hidden(fn ($state) => empty($state))
                                                    ->prefixIcon(fn ($state) => $state ? 'heroicon-m-check-badge' : null)
                                                    ->prefixIconColor('success')
                                                    ->suffixAction(
                                                        Action::make('open_drive')
                                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                                            ->color('primary')
                                                            ->tooltip('View on Google Drive')
                                                            ->url(fn ($state) => $state ? "https://drive.google.com/file/d/{$state}/view" : null)
                                                            ->openUrlInNewTab()
                                                            ->visible(fn ($state) => ! empty($state))
                                                    ),
                                                FileUpload::make('file_url')
                                                    ->label('Re/Upload ZIP File')
                                                    ->disk('google')
                                                    ->directory(config('filesystems.disks.google.folderName'))
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->required(fn ($record) => $record === null)
                                                    ->preserveFilenames()
                                                    ->live()
                                                    ->hidden(fn ($record) => ! empty($record?->file_url))
                                                    ->dehydrateStateUsing(function ($state) {
                                                        if (blank($state)) {
                                                            return null;
                                                        }
                                                        if (! str_contains($state, '/')) {
                                                            return $state;
                                                        }

                                                        return $state;
                                                    }),
                                            ])
                                            ->grid(3)
                                            ->addActionLabel('Add New Walkthrough File Uploader'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
