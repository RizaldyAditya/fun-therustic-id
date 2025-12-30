<?php
namespace App\Livewire;

use App\Models\Episode;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

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
                    ->label('Watch')
                    ->icon('heroicon-s-play-circle')
                    ->color('info')
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
                    )),
                EditAction::make('editEpisode')
                    ->label('Edit')
                    ->icon('heroicon-s-pencil')
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
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.episode-manager');
    }
}
