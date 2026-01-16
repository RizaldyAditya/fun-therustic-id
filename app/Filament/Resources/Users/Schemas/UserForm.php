<?php
namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        Section::make('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->inlineLabel(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->inlineLabel(),
                                FileUpload::make('avatar')
                                    ->label('Profile Photo')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->inlineLabel(),
                                TextInput::make('password')
                                    ->label('New Password')
                                    ->placeholder('Leave blank to keep current password')
                                    ->password()
                                    ->minLength(8)
                                    ->inlineLabel()
                                    ->rule('confirmed')
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state)),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->minLength(8)
                                    ->inlineLabel()
                                    ->visible(fn(string $context, $get) => $context === 'create' || filled($get('password')))
                                    ->required(fn(string $context, $get): bool => $context === 'create' || filled($get('password')))
                                    ->dehydrated(false),
                                Select::make('roles')
                                    ->label('Roles')
                                    ->multiple()
                                    ->relationship('roles', 'name')
                                    ->preload()
                                    ->inlineLabel(),
                            ]),
                    ]),
            ]);
    }
}
