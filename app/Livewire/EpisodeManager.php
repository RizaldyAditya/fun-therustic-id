<?php
namespace App\Livewire;

use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Support\Enums\Size;

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
                    ->type('number')
                    ->extraAttributes([
                        'style' => 'width: 50px;', // Sets the width of the cell
                    ])
                    ->extraHeaderAttributes([
                        'style' => 'width: 50px;', // Sets the width of the header to match
                    ])
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderByRaw('CAST(episode_number AS UNSIGNED) ' . $direction);
                    }),
                TextInputColumn::make('title')
                    ->label('Episode Title'),
                ImageColumn::make('stream.logo')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Stream')
                    ->alignCenter()
                    ->width(50)
                    ->url(fn($record) => $record->stream_url)
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                Action::make('viewVideoSourceUrl')
                    ->label('Watch Now')
                    ->icon('heroicon-s-play-circle')
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
            ]);
    }

    public function render()
    {
        return view('livewire.episode-manager');
    }
}
