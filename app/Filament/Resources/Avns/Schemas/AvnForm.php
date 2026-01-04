<?php
namespace App\Filament\Resources\Avns\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class AvnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                                    ->inlineLabel(),
                                TextInput::make('itch_io_url')
                                    ->label('itch.io URL')
                                    ->url()
                                    ->inlineLabel(),
                                FileUpload::make('cover_image')
                                    ->label('Cover Image')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('img/covers')
                                    ->inlineLabel(),
                                Select::make('rating')
                                    ->label('Rating')
                                    ->options([
                                        5 => '⭐⭐⭐⭐⭐ (Excellent)',
                                        4 => '⭐⭐⭐⭐ (Good)',
                                        3 => '⭐⭐⭐ (Average)',
                                        2 => '⭐⭐ (Poor)',
                                        1 => '⭐ (Terrible)',
                                    ])
                                    ->default(3)
                                    ->required()
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
                                    ->visible(fn(callable $get) => $get('genre_id') !== null),
                            ]),
                    ]),
                Grid::make()
                    ->columns(1)
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('status_id')
                                    ->relationship('status', 'name')
                                    ->inlineLabel()
                                    ->required(),
                                DatePicker::make('last_updated_on_itch')
                                    ->label('Last Updated on itch.io')
                                    ->native(false)
                                    ->displayFormat('Y-m-d')
                                    ->inlineLabel(),
                                TextInput::make('last_played_version')
                                    ->label('Last Played Version')
                                    ->inlineLabel(),
                                FileUpload::make('saves_file_url')
                                    ->label('Last Saves File (ZIP/RAR)')
                                    ->disk('google')
                                    ->directory('Adult Visual Novels [Saves]')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['application/zip', 'application/x-rar-compressed', 'application/x-zip-compressed'])
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable()
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) {
                                            return null;
                                        }
                                        return $state;
                                    })

                                    ->dehydrateStateUsing(function ($state) {
                                        if (!$state) {
                                            return null;
                                        }

                                        try {
                                            $fullUrl     = Storage::disk('google')->url($state);
                                            $queryString = parse_url($fullUrl, PHP_URL_QUERY);
                                            parse_str($queryString, $queryArray);
                                            return $queryArray['id'] ?? $state;
                                        } catch (\Exception $e) {
                                            return $state;
                                        }
                                    })
                                    ->maxSize(512000)
                                    ->inlineLabel()
                                    ->placeholder('No file uploaded')
                                    ->storeFileNamesIn('original_filename')
                                    ->preserveFilenames()
                                    ->extraAttributes([
                                        'type' => 'file',
                                    ]),
                                TextInput::make('saves_file_url_display')
                                    ->label('Current Google Drive ID')
                                    ->placeholder('No file in database')
                                    ->formatStateUsing(fn($record) => $record?->saves_file_url)
                                    ->readOnly()
                                    ->inlineLabel()
                                    ->suffixAction(
                                        Action::make('viewInDrive')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->url(fn($state) => $state ? "https://drive.google.com/file/d/{$state}/view?usp=sharing" : null)
                                            ->openUrlInNewTab()
                                            ->visible(fn($state) => filled($state))
                                    ),
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
                                    ->rows(12),
                            ]),
                    ]),
            ]);
    }
}
