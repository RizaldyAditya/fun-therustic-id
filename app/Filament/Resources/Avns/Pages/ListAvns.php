<?php

namespace App\Filament\Resources\Avns\Pages;

use App\Filament\Resources\Avns\AvnResource;
use App\Models\Avn;
use App\Models\Status;
use App\Models\VarEntry;
use App\Traits\SociGames;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListAvns extends ListRecords
{
    use SociGames;

    protected static string $resource = AvnResource::class;

    public ?array $importingItem = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('socigames')
                ->label('SociGames')
                ->icon('heroicon-s-globe-alt')
                ->color('success')
                ->modalHeading('SociGames Latest Updates')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalWidth('full')
                ->modalContent(function () {
                    $url = VarEntry::where('name', 'socigames_renpy_update')->value('value');
                    $items = self::getLatestItems($url);

                    return view('filament.socigames-modal', [
                        'items' => $items,
                    ]);
                }),
            CreateAction::make(),
        ];
    }

    public function importSocigamesItem(array $item): void
    {
        $this->importingItem = $item;

        $defaultStatusId = Status::where('slug', 'to-download')->first()?->id;

        Action::make('importSocigames')
            ->label('Import from SociGames')
            ->icon('heroicon-s-arrow-down-tray')
            ->modalHeading('Import: '.$item['title'])
            ->modalDescription('Fill in the details below to import this AVN.')
            ->modalSubmitActionLabel('Import')
            ->modalWidth('3xl')
            ->schema([
                TextInput::make('import_title')
                    ->label('Title')
                    ->required()
                    ->default($item['title'])
                    ->live(onBlur: true),
                TextInput::make('import_version')
                    ->label('Latest Version')
                    ->required()
                    ->default($item['version']),
                TextInput::make('import_developer')
                    ->label('Developer')
                    ->required()
                    ->autofocus(),
                Select::make('import_status_id')
                    ->label('Status')
                    ->options(Status::pluck('name', 'id'))
                    ->required()
                    ->default($defaultStatusId),
                TextInput::make('import_socigames_url')
                    ->label('SociGames URL')
                    ->url()
                    ->default($item['url']),
                FileUpload::make('import_cover_image')
                    ->label('Cover Image')
                    ->image()
                    ->disk('public')
                    ->directory('img/avn-covers')
                    ->default($item['image'])
                    ->downloadable()
                    ->previewable(),
            ])
            ->action(function (array $data): void {
                $socigamesUrl = $data['import_socigames_url'] ?? null;

                // Check for duplicate by socigames_url
                if ($socigamesUrl && Avn::where('socigames_url', $socigamesUrl)->exists()) {
                    Notification::make()
                        ->title('AVN Already Exists')
                        ->body('An AVN with this SociGames URL already exists. Skipping import.')
                        ->warning()
                        ->send();

                    $this->dispatch('closeModal', name: 'importSocigames');

                    return;
                }

                $coverPath = null;

                // Handle cover image: download from URL or use uploaded file
                if (! empty($data['import_cover_image'])) {
                    if (str_starts_with($data['import_cover_image'], 'http')) {
                        // Download from external URL
                        $coverPath = self::downloadCoverImage(
                            $data['import_cover_image'],
                            $data['import_title']
                        );
                    } else {
                        // Already uploaded via FileUpload
                        $coverPath = $data['import_cover_image'];
                    }
                }

                Avn::create([
                    'title' => $data['import_title'],
                    'developer' => $data['import_developer'],
                    'version' => $data['import_version'],
                    'status_id' => $data['import_status_id'],
                    'socigames_url' => $socigamesUrl,
                    'cover_image' => $coverPath,
                    'last_played_version' => null,
                ]);

                Notification::make()
                    ->title('AVN Imported')
                    ->body("{$data['import_title']} has been imported successfully.")
                    ->success()
                    ->send();

                $this->dispatch('closeModal', name: 'importSocigames');
            })
            ->dispatch();
    }

    private static function downloadCoverImage(string $url, string $title): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);

            if ($response->failed()) {
                return null;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
            $filename = Str::slug($title).'.'.$extension;
            $path = 'img/avn-covers/'.$filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to download cover image: '.$e->getMessage(), [
                'url' => $url,
            ]);

            return null;
        }
    }
}
