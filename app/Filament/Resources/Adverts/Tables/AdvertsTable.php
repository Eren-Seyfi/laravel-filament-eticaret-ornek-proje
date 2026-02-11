<?php

namespace App\Filament\Resources\Adverts\Tables;

use App\Enums\AdvertPlacement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdvertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('priority', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->disk('public_root')
                    ->square()
                    ->size(48)
                    ->toggleable()
                    ->defaultImageUrl(url('/images/placeholder.svg')),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                SelectColumn::make('placement')
                    ->label('Konum')
                    ->options(AdvertPlacement::options())
                    ->sortable(),

                TextColumn::make('external_url')
                    ->label('Link')
                    ->placeholder('-')
                    ->icon(Heroicon::GlobeAlt)
                    ->iconPosition(IconPosition::Before)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('video')
                    ->label('Video')
                    ->hidden()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextInputColumn::make('priority')
                    ->label('Öncelik')
                    ->type('number')
                    ->inputMode('decimal')
                    ->step('1')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_forever')
                    ->label('Süresiz')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('placement')
                    ->label('Konum')
                    ->options(AdvertPlacement::options()),

                TernaryFilter::make('is_active')->label('Aktif mi?'),
                TernaryFilter::make('is_forever')->label('Süresiz mi?'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
