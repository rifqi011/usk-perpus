<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ])
            ->brandName(function () {
                try {
                    $siteSetting = SiteSetting::getInstance();
                    if ($siteSetting) {
                        $brandName = $siteSetting->site_name ?? 'Perpustakaan';
                        $logoUrl = $siteSetting->site_logo 
                            ? asset('storage/' . $siteSetting->site_logo) 
                            : null;
                        $tagline = $siteSetting->site_tagline ?? null;
                        
                        $html = '<div class="flex items-center gap-3">';
                        
                        // Logo
                        if ($logoUrl) {
                            $html .= '<img src="' . $logoUrl . '" alt="Logo" class="h-10 w-auto object-contain" />';
                        }
                        
                        // Nama brand dan tagline
                        $html .= '<div class="flex flex-col">';
                        $html .= '<span class="text-base font-bold tracking-tight">' . e($brandName) . '</span>';
                        
                        if ($tagline) {
                            $html .= '<span class="text-xs text-gray-500 dark:text-gray-400 -mt-0.5">' . e($tagline) . '</span>';
                        }
                        
                        $html .= '</div>';
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    }
                    return 'Perpustakaan';
                } catch (\Exception $e) {
                    return 'Perpustakaan';
                }
            })
            ->brandLogo(null)
            ->brandLogoHeight('auto')
            ->favicon(fn () => SiteSetting::getInstance()->site_favicon 
                ? asset('storage/' . SiteSetting::getInstance()->site_favicon) 
                : asset('favicon.ico'))
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Profil Saya')
                    ->url(fn (): string => \App\Filament\Pages\AdminProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->defaultAvatarProvider(\Filament\AvatarProviders\UiAvatarsProvider::class)
            ->renderHook(
                'panels::body.end',
                fn () => Blade::render('<script>
                    setInterval(() => {
                        const clockElement = document.getElementById("filament-clock");
                        if (clockElement) {
                            const now = new Date();
                            const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                            const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                            
                            const dayName = days[now.getDay()];
                            const date = now.getDate();
                            const month = months[now.getMonth()];
                            const year = now.getFullYear();
                            const hours = String(now.getHours()).padStart(2, "0");
                            const minutes = String(now.getMinutes()).padStart(2, "0");
                            const seconds = String(now.getSeconds()).padStart(2, "0");
                            
                            clockElement.innerHTML = `
                                <div class="text-xs text-gray-600 dark:text-gray-400 text-right">
                                    <div class="font-medium">${dayName}, ${date} ${month} ${year}</div>
                                    <div class="text-sm font-semibold">${hours}:${minutes}:${seconds}</div>
                                </div>
                            `;
                        }
                    }, 1000);
                </script>')
            )
            ->renderHook(
                'panels::user-menu.before',
                fn () => Blade::render('<div id="filament-clock" class="px-4"></div>')
            )
            ->navigationGroups([
                'Manajemen User',
                'Master Data',
                'Transaksi',
                'Pengaturan',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
