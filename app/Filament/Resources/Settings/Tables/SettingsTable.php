<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site_name')
                    ->label('Site Adı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('site_tagline')
                    ->label('Slogan')
                    ->searchable()
                    ->toggleable(),

                // ✅ LOGO: yatay görselleri tam göstermek için contain
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public_root')
                    ->height(36) // sabit yükseklik
                    ->extraImgAttributes([
                        'style' => 'width: 140px; object-fit: contain; background: rgba(0,0,0,.03); padding: 4px; border-radius: 8px;',
                    ])
                    ->toggleable()
                    ->defaultImageUrl(url('/images/logo.svg')),

                // ✅ FAVICON: kare küçük ikon gibi
                ImageColumn::make('favicon_path')
                    ->label('Favicon')
                    ->disk('public_root')
                    ->height(24)
                    ->extraImgAttributes([
                        'style' => 'width: 24px; height: 24px; object-fit: contain; background: rgba(0,0,0,.03); padding: 2px; border-radius: 6px;',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->defaultImageUrl(url('/images/favicon.svg')),

                // ✅ OG: banner gibi (geniş)
                ImageColumn::make('og_image_path')
                    ->label('OG')
                    ->disk('public_root')
                    ->height(36)
                    ->extraImgAttributes([
                        'style' => 'width: 160px; object-fit: contain; background: rgba(0,0,0,.03); padding: 4px; border-radius: 8px;',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->defaultImageUrl(url('/images/og-image.svg')),

                TextColumn::make('seo_title')
                    ->label('SEO Başlık')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('contact_email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('search_engine_indexing')
                    ->label('Index')

                    ->toggleable(),

                ToggleColumn::make('robots_txt_enabled')
                    ->label('robots.txt')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('maintenance_mode')
                    ->label('Bakım')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Güncellendi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
