<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->topNavigation()
            ->id('admin')
            ->path('/')
            ->viteTheme([
                'resources/css/filament/admin/theme.css',
                'resources/css/app.css',
            ])
            ->login()
            ->profile(isSimple: false, page: EditProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(asset('favicon.ico'.'?v=2024-06-03'))
            // ->brandLogo(asset('favicon-v2.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            // ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Dashboard')
                    ->icon('heroicon-s-home'),
                NavigationGroup::make()
                    ->label('Movies')
                    ->icon('heroicon-s-circle-stack')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Visual Novels')
                    ->icon('heroicon-s-cube-transparent')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Management')
                    ->icon('heroicon-s-users')
                    ->collapsed(),
            ])
            ->maxContentWidth(Width::Full)
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}
