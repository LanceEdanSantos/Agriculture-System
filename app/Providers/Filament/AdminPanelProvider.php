<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Spatie\Permission\Models\Role;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->databaseNotifications()
            ->id('admin')
            ->path('admin')
            ->login()
            // ->authMiddleware([
            //     \App\Http\Middleware\EnsureUserIsNotFarmer::class . ':Farmer',
            // ], isPersistent: true)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->databaseNotificationsPolling('5s')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                ->navigationLabel('Roles and Permissions')
                ->navigationIcon('heroicon-o-home')
                ->activeNavigationIcon('heroicon-s-home')
                ->navigationGroup('User Management')
                ->navigationSort(10)
                ->navigationBadge()
                ->navigationBadgeColor('success')
               
                ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])  
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
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
