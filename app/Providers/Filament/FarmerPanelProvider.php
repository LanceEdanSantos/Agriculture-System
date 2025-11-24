<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FarmerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('farmer')
            ->path('/')
            ->login()
            ->topNavigation()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Farmer/Resources'), for: 'App\Filament\Farmer\Resources')
            ->discoverPages(in: app_path('Filament/Farmer/Pages'), for: 'App\Filament\Farmer\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->authMiddleware([
                \App\Http\Middleware\EnsureUserHasRole::class . ':Farmer',
            ], isPersistent: true)
            ->discoverWidgets(in: app_path('Filament/Farmer/Widgets'), for: 'App\Filament\Farmer\Widgets')
            ->widgets([
                AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->colors([
                'primary' => Color::Green,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'info' => Color::Blue,
            ])
            ->brandLogo(asset('images/PAO.png'))
            ->brandLogoHeight('6rem')
            ->favicon(asset('favicon.ico'))
            ->font('Inter')
            ->brandName('Agrostock')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
