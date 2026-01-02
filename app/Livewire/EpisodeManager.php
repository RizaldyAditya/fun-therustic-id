<?php
namespace App\Livewire;

use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class EpisodeManager extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $donghuaId;
    public $streamId;

    public function table(Table $table): Table
    {
        return $table
            ->query(Episode::query()
                    ->where('donghua_id', $this->donghuaId)
                    ->where('stream_id', $this->streamId)
            )
            ->columns([
                TextInputColumn::make('episode_number')
                    ->label('# Episode')
                    // ->type('number')
                    ->extraAttributes([
                        'style' => 'width: 50px;',
                    ])
                    ->extraHeaderAttributes([
                        'style' => 'width: 50px;',
                    ])
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderByRaw('CAST(episode_number AS UNSIGNED) ' . $direction);
                    }),
                TextColumn::make('title')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('notes')
                    ->wrap()
                    ->searchable(),
                ImageColumn::make('stream.logo')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Stream URL')
                    ->alignEnd()
                    ->imageHeight(30)
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab()
                    ->extraHeaderAttributes([
                        'style' => 'text-align: center; width: 50px;',
                    ]),
            ])
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('')
                    ->icon('heroicon-s-play')
                    ->color('success')
                    ->size(Size::Large)
                    ->slideOver()
                    ->modalHeading(fn($record) => $record->title)
                    ->modalDescription(fn($record) => $record->donghua->title_en)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('4xl')
                    ->modalContent(fn($record): View => view(
                        'filament.iframe-field',
                        ['url' => $record->video_source_url]
                    ))
                    ->tooltip('Watch Now'),
                Action::make('copy_url')
                    ->label('')
                    ->icon('heroicon-s-clipboard-document-list')
                    ->color('primary')
                    ->tooltip('Copy video source URL to clipboard.')
                    ->action(function ($record, $livewire) {
                        $livewire->js("
                            window.navigator.clipboard.writeText('{$record->video_source_url}');
                            new FilamentNotification()
                                .title('URL copied to clipboard')
                                .success()
                                .send();
                        ");
                    }),
                EditAction::make('editEpisode')
                    ->label('')
                    ->slideOver()
                    ->icon('heroicon-s-pencil-square')
                    ->color('warning')
                    ->schema([
                        TextInput::make('episode_number')
                            ->label('# Episode')
                            ->type('number')
                            ->required(),
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        TextInput::make('notes')
                            ->label('Notes')
                            ->nullable(),
                        TextInput::make('stream_url')
                            ->label('Stream URL')
                            ->url()
                            ->required(),
                        TextInput::make('video_source_url')
                            ->label('Video Source URL')
                            ->url()
                            ->required(),
                    ])
                    ->modalWidth('4xl')
                    ->modalHeading(fn($record) => "Edit Episode: {$record->title}")
                    ->action(function (Episode $record, array $data) {
                        $record->update($data);
                        Notification::make()
                            ->title('Episode data saved successfully.')
                            ->success() // Green color
                            ->send();
                    })
                    ->tooltip('Edit'),
                DeleteAction::make('deleteEpisode')
                    ->label('')
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Episode $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Episode deleted successfully.')
                            ->success() // Green color
                            ->send();
                    })
                    ->tooltip('Delete'),
            ])
            ->toolbarActions([
                BulkAction::make('deleteEpisodes')
                    ->label('Delete Selected')
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->delete();
                        Notification::make()
                            ->title('Selected episodes deleted successfully.')
                            ->success() // Green color
                            ->send();
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.episode-manager');
    }
}
