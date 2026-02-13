<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $defaultLogo = asset('images/logo.svg');

        /**
         * 1) Artisan komutlarında (migrate/optimize:clear vs) DB/Cache'e dokunma.
         *    Aksi halde cache tablosu yok / db yetkisi yok gibi durumlarda tüm komutlar kilitlenir.
         */
        if (App::runningInConsole()) {
            return $this->configurePanel($panel, $defaultLogo, config('app.name'));
        }

        /**
         * 2) Web request'te: DB/Cache hazır değilse de patlatma.
         *    Cache store database ise 'cache' tablosu yokken Cache::remember patlar.
         *    Bu yüzden önce güvenli kontroller + try/catch.
         */
        $brandLogoUrl = $defaultLogo;
        $brandName = config('app.name');

        try {
            // settings tablosu yoksa (ilk kurulum) DB'ye hiç girmeyelim.
            if (Schema::hasTable('settings')) {
                // Cache store DB olsa bile, cache tablosu yoksa remember patlar.
                // Bu yüzden cache tablosu var mı kontrol ederek cache'i devreye alıyoruz.
                if ($this->canUseDatabaseCacheSafely()) {
                    $brandLogoUrl = Cache::remember('filament.admin.brand_logo_url', 60, function () use ($defaultLogo) {
                        $path = Setting::query()->value('logo_path');

                        if (!$path) {
                            return $defaultLogo;
                        }

                        return asset(ltrim($path, '/'));
                    });

                    $brandName = Cache::remember('filament.admin.brand_name', 60, function () {
                        return Setting::query()->value('site_name') ?: config('app.name');
                    });
                } else {
                    // Cache'i es geç, direkt DB'den oku (hata olursa catch'e düşer)
                    $path = Setting::query()->value('logo_path');
                    $brandLogoUrl = $path ? asset(ltrim($path, '/')) : $defaultLogo;

                    $brandName = Setting::query()->value('site_name') ?: config('app.name');
                }
            }
        } catch (\Throwable $e) {
            // Her türlü DB/cache hatasında güvenli fallback:
            $brandLogoUrl = $defaultLogo;
            $brandName = config('app.name');
        }

        return $this->configurePanel($panel, $brandLogoUrl, $brandName);
    }

    /**
     * Cache store database iken 'cache' tablosu yoksa Cache::remember patlar.
     * Burada, "database cache kullanıyorsak tablo var mı?" kontrolü yapıyoruz.
     */
    private function canUseDatabaseCacheSafely(): bool
    {
        try {
            $defaultStore = config('cache.default'); // Laravel 12: CACHE_STORE -> cache.default

            // database store aktifse cache tablosu var mı kontrol et
            if ($defaultStore === 'database') {
                $table = config('cache.stores.database.table', 'cache');

                return Schema::hasTable($table);
            }

            // file/redis/memcached vs: safe
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function configurePanel(Panel $panel, string $brandLogoUrl, string $brandName): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName($brandName)
            ->brandLogo($brandLogoUrl)
            ->brandLogoHeight('2.25rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ]);
    }
}
