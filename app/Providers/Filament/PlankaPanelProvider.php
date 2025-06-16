<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Planka\Pages\Auth\Login;
use App\Models\Planka\AuthenticatableUserAccount;
use App\Http\Middleware\CheckPlankaUserActive;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PlankaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('planka')
            ->path('planka')
            ->maxContentWidth('full')
            ->login(Login::class)
            ->authGuard('web')
            ->brandName('Planka Companion')
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Gray,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
            ])
            ->font('Inter')
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Planka/Resources'), for: 'App\\Filament\\Planka\\Resources')
            ->discoverPages(in: app_path('Filament/Planka/Pages'), for: 'App\\Filament\\Planka\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Planka/Widgets'), for: 'App\\Filament\\Planka\\Widgets')
            ->widgets([])
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
            ->authMiddleware([
                Authenticate::class,
                CheckPlankaUserActive::class,
            ]);
    }
}
