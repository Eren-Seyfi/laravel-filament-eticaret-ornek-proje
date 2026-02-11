<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use App\Models\TrackEvent;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ürün Bilgileri')
                ->description('Ürünün temel bilgileri.')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')
                        ->label('Ad')
                        ->placeholder('-'),

                    TextEntry::make('category.name')
                        ->label('Kategori')
                        ->placeholder('-'),

                    TextEntry::make('price')
                        ->label('Fiyat')
                        ->placeholder('-'),

                    TextEntry::make('currency')
                        ->label('Para Birimi')
                        ->placeholder('-'),

                    TextEntry::make('external_url')
                        ->label('Dış Bağlantı')
                        ->placeholder('-'),

                    TextEntry::make('is_active')
                        ->label('Durum')
                        ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Pasif')
                        ->badge()
                        ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                    TextEntry::make('created_at')
                        ->label('Oluşturma')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label('Güncelleme')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),
                ]),

            Section::make('Günlük İstatistik')
                ->description('Bugün (gün içinde) ürünün aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_daily_views')
                        ->label('Günlük Görüntülenme')
                        ->state(fn(Product $record): int => TrackEvent::countViewsToday($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_daily_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Product $record): int => TrackEvent::countUniqueViewsToday($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_daily_clicks')
                        ->label('Günlük Tıklama')
                        ->state(fn(Product $record): int => TrackEvent::countClicksToday($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Haftalık İstatistik')
                ->description('Bu hafta (Pazartesi–Pazar) ürünün aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_weekly_views')
                        ->label('Haftalık Görüntülenme')
                        ->state(fn(Product $record): int => TrackEvent::countViewsThisWeek($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_weekly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Product $record): int => TrackEvent::countUniqueViewsThisWeek($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_weekly_clicks')
                        ->label('Haftalık Tıklama')
                        ->state(fn(Product $record): int => TrackEvent::countClicksThisWeek($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Aylık İstatistik')
                ->description('Bu ay (ayın 1’i–ayın sonu) ürünün aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_monthly_views')
                        ->label('Aylık Görüntülenme')
                        ->state(fn(Product $record): int => TrackEvent::countViewsThisMonth($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_monthly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Product $record): int => TrackEvent::countUniqueViewsThisMonth($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_monthly_clicks')
                        ->label('Aylık Tıklama')
                        ->state(fn(Product $record): int => TrackEvent::countClicksThisMonth($record))
                        ->badge()
                        ->color('warning'),
                ]),
        ]);
    }
}
