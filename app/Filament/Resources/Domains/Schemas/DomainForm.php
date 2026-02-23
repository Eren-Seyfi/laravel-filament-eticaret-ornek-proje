<?php

namespace App\Filament\Resources\Domains\Schemas;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('host')
                ->label('Domain')
                ->placeholder('example.com')
                ->helperText('Sadece domain yazın. Örn: example.com (https:// veya /path yazmayın, www otomatik temizlenir)')
                ->required()
                ->maxLength(255)
                // basit domain format kontrolü (www / http(s) / path vs gelirse normalize edeceğiz)
                ->rule('regex:/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i')
                ->dehydrateStateUsing(fn($state) => self::normalizeHost($state))
                ->formatStateUsing(fn($state) => $state ? self::normalizeHost($state) : $state)
                ->unique(ignoreRecord: true),

            Toggle::make('is_active')
                ->label('Aktif mi?')
                ->default(true)
                ->inline(false),

            Toggle::make('is_canonical')
                ->label('Canonical (Ana Domain)')
                ->default(false)
                ->inline(false)
                // canonical true seçilince aktif zorunlu olsun
                ->afterStateUpdated(function (Closure $set, $state) {
                    if ($state) {
                        $set('is_active', true);
                    }
                })
                ->helperText('Sadece 1 domain canonical olmalı. Canonical seçersen otomatik aktif yapılır.'),
        ]);
    }

    /**
     * Girilen değeri güvenli şekilde "host" formatına çevirir:
     * - https://, http:// kaldırır
     * - /path?query siler
     * - port kaldırır
     * - baştaki www. kaldırır
     * - trim + lowercase
     */
    protected static function normalizeHost(?string $host): string
    {
        $host = strtolower(trim((string) $host));

        // Şema kaldır
        $host = preg_replace('#^https?://#i', '', $host) ?? $host;

        // Her şeyi ilk / sonrası kes (path/query)
        $host = preg_replace('#/.*$#', '', $host) ?? $host;

        // Port kaldır (example.com:8080)
        $host = preg_replace('#:\d+$#', '', $host) ?? $host;

        // www kaldır
        $host = preg_replace('#^www\.#i', '', $host) ?? $host;

        return $host;
    }
}