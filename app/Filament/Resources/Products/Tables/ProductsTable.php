<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\CurrencyCode;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public_root')   // ✅ public/
                    ->size(44)
                    ->defaultImageUrl(url('/images/placeholder.svg')),

                TextColumn::make('name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money(fn($record) => $record->currency?->value ?? CurrencyCode::TRY ->value)
                    ->sortable(),

                TextColumn::make('rating_stars')
                    ->label('Puan')
                    ->state(fn($record) => $record->rating_stars ?? 0)
                    ->icon(Heroicon::Star)
                    ->iconColor('warning')
                    ->iconPosition(IconPosition::Before)
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                ToggleColumn::make('availability_forever')
                    ->label('Sürekli')
                    ->sortable(),

                TextColumn::make('availability_starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('availability_ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('external_url')
                    ->label('Yönlendirilecek Site')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextInputColumn::make('sort_order')
                    ->label('Sıra')
                    ->type('number')
                    ->inputMode('decimal')
                    ->step('1')
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
                TernaryFilter::make('is_active')->label('Aktif mi?'),
                TernaryFilter::make('availability_forever')->label('Sürekli mi?'),
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
